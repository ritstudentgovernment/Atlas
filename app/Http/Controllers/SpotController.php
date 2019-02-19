<?php

namespace App\Http\Controllers;

use App\Category;
use App\Classification;
use App\Descriptors;
use App\Spot;
use App\Type;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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
        return $spots->filter(function (Spot $spot) use ($user) {
            $requiredViewPermission = $spot->classification->view_permission;
            if ($user) {
                if (!$spot->approved && $user->can('view unapproved spots')) {
                    return true;
                }

                return $spot->approved && $user->can($requiredViewPermission);
            }

            return $spot->approved && !$requiredViewPermission;
        })->values()->all();
    }

    private function filterClassifications(Collection $classifications, User $user = null)
    {
        return $classifications->filter(function (Classification $classification) use ($user) {
            $requiredCreatePermission = $classification->create_permission;
            if ($user == null) {
                // The user is not logged in, filter out all classifications. (should never happen)
                return false;
            } else {
                return $user->can($requiredCreatePermission);
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
        $validatedDescriptors = [];
        $sentDescriptors = collect();
        $requestDescriptors = $request->input('descriptors');
        $categoryRequiredDescriptors = $type->category->descriptors;
        // Loop through all of the sent descriptors to verify they are required by the spots category
        foreach ($requestDescriptors as $descriptorId => $value) {
            if ($descriptor = Descriptors::find($descriptorId)) {
                // Make sure the descriptor exists
                if ($categoryRequiredDescriptors->pluck('id')->contains($descriptorId)) {
                    // Verify the value is one of the allowed values
                    $allowedValues = collect(explode('|', $descriptor->allowed_values));
                    if ($allowedValues->contains($value)) {
                        $sentDescriptors->push($descriptor);
                        $validatedDescriptors[$descriptorId] = ['value' => $value];
                    } else {
                        $validator->errors()->add('Descriptors', "Invalid value, $value, supplied for descriptor $descriptorId");
                    }
                } else {
                    Log::debug('Descriptor sent with creation of spot is not required for the sent type');
                }
            } else {
                $validator->errors()->add('Descriptors', "Descriptor $descriptorId does not exist");
            }
        }
        // Compare the descriptors that the category requires with the sent descriptors to make sure no missing required descriptors
        $missingDescriptors = $categoryRequiredDescriptors->diff($sentDescriptors);
        if ($missingDescriptors->count()) {
            $validator->errors()->add('Missing Descriptors', $missingDescriptors->pluck('name')->toJson());
        }

        return ['descriptors' => $validatedDescriptors, 'validator' => $validator];
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
            'notes'             => 'sometimes|string|nullable',
            'descriptors'       => 'required',
            'type_name'         => 'required',
            'lat'               => 'required|numeric',
            'lng'               => 'required|numeric',
            'classification_id' => 'required|numeric',
        ];

        $validator = \Illuminate\Support\Facades\Validator::make(Input::all(), $rules);

        // Initial validation, just that required fields are sent
        if ($validator->fails()) {
            return $validator->errors();
        }

        $type = Type::where('name', $request->input('type_name'))->first();
        $classification = Classification::find($request->input('classification_id'));

        if (!$type || !$classification) {
            $validator->errors()->add('Invalid Error', 'You\'ve provided an invalid type or classification.');
            return $validator->errors();
        }

        $validatedRequestDescriptors = $this->validateSentDescriptors($request, $type, $validator);

        $validatedDescriptors = $validatedRequestDescriptors['descriptors'];
        $validator = $validatedRequestDescriptors['validator'];

        $validator = $this->checkUserCanMakeRequestedSpot($request->user(), $type->category, $validator);

        if ($validator->fails()) {
            return $validator->errors();
        }

        $spot = Spot::create([

            'notes'             => $request->input('notes'),
            'type_id'           => $type->id,
            'lat'               => $request->input('lat'),
            'lng'               => $request->input('lng'),
            'approved'          => $request->user()->can('approve spots') ? 1 : 0,
            'user_id'           => $request->user()->id,
            'classification_id' => $classification->id,

        ]);

        if ($spot instanceof Spot) {
            $spot->descriptors()->attach($validatedDescriptors);
        }

        return $spot;
    }

    public function getDefaults(Request $request)
    {
        $categories = Category::with(['classifications', 'descriptors', 'types'])->get();

        if ($categoryName = $request->input('category')) {
            $category = Category::where('name', $categoryName)->first();
        } else {
            $category = $categories->first();
        }

        $descriptors = $category->descriptors;
        $classifications = $category->classifications;
        $types = $category->types;

        $requiredSpotData = [

            'lat'               => 'number',
            'lng'               => 'number',

            'notes'             => 'string',
            'descriptors'       => 'object',

            'type_id'           => 'number',
            'classification_id' => 'number',

        ];

        return [
            'requiredData'              => $requiredSpotData,
            'availableTypes'            => $types,
            'requiredDescriptors'       => $descriptors,
            'availableClassifications'  => $this->filterClassifications($classifications, auth('api')->user()),
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
