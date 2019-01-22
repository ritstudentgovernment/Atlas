<?php

namespace App\Http\Controllers;

use App\Category;
use App\Descriptors;
use App\Spot;
use App\Type;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Validator;

class SpotController extends Controller
{
    /**
     * Constructor to prevent unauthenticated access to sensitive routes.
     */
    public function __construct()
    {
        $this->middleware('auth:api')->except(['get']);
    }

    private function filterSpotsVisible(Collection $spots, User $user = null)
    {
        return $spots->reject(function (Spot $spot) use ($user) {
            $requiredViewPermission = $spot->classification->view_permission;
            if ($user == null) { // User is not logged in

                // Remove spots that are not approved or that have a required permission
                return !$spot->approved || $requiredViewPermission;
            } else {
                if (!$spot->approved && !$user->can('view unapproved spots')) {
                    return true;
                }

                return !$user->can($requiredViewPermission);
            }
        })->values()->all();
    }

    /**
     * Function to get the nap spots the current user may see.
     *
     * @param Request $request The Http request.
     *
     * @return Collection The collection of spots a user is supposed to see.
     */
    public function get(Request $request)
    {
        return $this->filterSpotsVisible(Spot::all(), auth('api')->user());
    }

    private function validateSentDescriptors(Request $request, Type $type, Validator $validator)
    {
        $validatedRequestDescriptors = [];
        $requestDescriptors = $request->input('descriptors');
        $categoryRequiredDescriptors = $type->category->descriptors;
        // Loop through all of the sent descriptors and make sure they're supposed to be there and that required ones exist
        foreach ($requestDescriptors as $descriptorId => $value) {
            // Make sure the descriptor actually exists
            if (!($descriptor = Descriptors::find($descriptorId))) {
                $validator->errors()->add('Descriptors', "Descriptor $descriptorId does not exist");
            } else {
                // Make sure the descriptor is one of the Category's required descriptors
                if ($categoryRequiredDescriptors->pluck('id')->contains($descriptorId)) {
                    $validatedRequestDescriptors[$descriptorId] = ['descriptor'=>$descriptor, 'value'=>$value];
                } else {
                    Log::debug('Descriptor sent with creation of spot is not required for the sent type');
                }
            }
        }
        $missingDescriptors = $categoryRequiredDescriptors->diff(collect($validatedRequestDescriptors)->pluck('descriptor'));
        if ($missingDescriptors->count()) {
            $validator->errors()->add('Missing Descriptors', $missingDescriptors->pluck('name')->toJson());
        }
        if ($validatedRequestDescriptors instanceof Collection) {
            $validatedRequestDescriptors = $validatedRequestDescriptors->map(function ($item) {
                return [$item['descriptor']->id => $item['value']];
            });
        }

        return ['descriptors' => $validatedRequestDescriptors, 'validator' => $validator];
    }

    private function checkUserCanMakeRequestedSpot(User $user, Category $category, Validator $validator)
    {
        if (!$user->can('make designated spots')) {
            if ($category->croudsource) {
                return $validator;
            }
            $validator->errors()->add('Permission Error', 'The category you specified does not allow croudsourced spots.');
        }

        return $validator;
    }

    /**
     * Endpoint hit for creating nap spots.
     *
     * @param Request $request The HTTP request.
     *
     * @return Spot|MessageBag The new spot or the errors that occurred while parsing the request.
     */
    public function store(Request $request)
    {
        $rules = [
            'title'         => 'required',
            'notes'         => 'required',
            'building'      => 'required',
            'descriptors'   => 'required',
            'floor'         => 'required|numeric',
            'type_id'       => 'required|numeric',
            'lat'           => 'required|numeric',
            'lng'           => 'required|numeric',
        ];

        $validator = \Illuminate\Support\Facades\Validator::make(Input::all(), $rules);

        // Initial validation, just that required fields are sent
        if ($validator->fails()) {
            return $validator->errors();
        }

        $type = Type::find($request->input('type_id'));
        $validatedRequestDescriptors = $this->validateSentDescriptors($request, $type, $validator);

        $validatedDescriptors = $validatedRequestDescriptors['descriptors'];
        $validator = $validatedRequestDescriptors['validator'];

        $validator = $this->checkUserCanMakeRequestedSpot($request->user(), $type->category, $validator);

        if ($validator->fails()) {
            return $validator->errors();
        }

        $spot = Spot::create([

            'title'     => $request->input('title'),
            'notes'     => $request->input('notes'),
            'building'  => $request->input('building'),
            'floor'     => $request->input('floor'),
            'type_id'   => $request->input('type_id'),
            'lat'       => $request->input('lat'),
            'lng'       => $request->input('lng'),
            'approved'  => $request->user()->can('approve spots') ? 1 : 0,
            'user_id'   => $request->user()->id,

        ]);

        if ($spot instanceof Spot) {
            $spot->descriptors()->attach($validatedDescriptors);
        }

        return $spot;
    }

    public function getDefaults(Request $request)
    {
        $categories = Category::all();

        if ($categoryName = $request->input('category')) {
            $category = Category::where('name', $categoryName)->first();
        } else {
            $category = $categories->first();
        }

        $descriptors = $category->descriptors;
        $classifications = $category->classifications;
        $types = $category->types;

        $requiredSpotData = [

            'lat'               => null,
            'lng'               => null,
            'building'          => null,
            'floor'             => null,

            'title'             => null,
            'notes'             => null,
            'approved'          => null,

            'user_id'           => null,
            'type_id'           => null,

            'descriptors'       => null,
            'classification_id' => null,

        ];

        return [
            'requiredData'              => $requiredSpotData,
            'availableTypes'            => $types,
            'requiredDescriptors'       => $descriptors,
            'availableClassifications'  => $classifications,
            'availableCategories'       => $categories,
        ];
    }

    /**
     * Endpoint hit to approve a nap spot.
     *
     * @param Request $request the http request.
     * @param Spot    $spot    the spot to try and approve
     *
     * @return void
     */
    public function approve(Request $request, Spot $spot)
    {
        $spot->approved = true;
        $spot->save();
    }
}
