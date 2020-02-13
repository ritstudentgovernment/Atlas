<?php

namespace App\Http\Controllers;

use App\Category;
use App\Classification;
use App\Descriptors;
use App\Events\Spots\Approved;
use App\Events\Spots\Created;
use App\Events\Spots\Rejected;
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
    private $validator;

    /**
     * Constructor to prevent unauthenticated access to sensitive routes.
     */
    public function __construct()
    {
        $this->middleware('auth:api')->except(['get']);
    }

    /**
     * This function is responsible for figuring out what spots a user can see based on their permissions and the status
     * of each spot.
     *
     * It produces the following output:
     *
     * 1. If a user is not logged in, this function will only return approved spots with active categories and no
     *    required view permission.
     * 2. If a normal user is logged in, this function will return spots they have authored, whether approved or not,
     *    and approved spots with active categories and no required view permission (unless a user has been granted a
     *    special permission, in which case they will see approved and active spots restricted to that permission too).
     * 3. Reviewers / Admins / people with the 'view unapproved spots' permission will see all spots that are not
     *    approved.
     * 4. Admins / people with the 'view inactive spots' permission will see all spots.
     *
     * @param Collection $spots
     * @param User|null  $user
     *
     * @return array
     */
    private function filterSpotsVisible(Collection $spots, User $user = null)
    {
        return $spots->filter(function (Spot $spot) use ($user) {
            $spotIsApproved = $spot->approved;
            $spotCategoryIsActive = $spot->category->active;
            $requiredViewPermission = $spot->classification->view_permission;
            if ($user) {
                if (!$spotIsApproved && $user->can('view unapproved spots')) {
                    return true;
                } elseif (!$spotCategoryIsActive && $user->can('view inactive spots')) {
                    return true;
                }

                $userIsAuthor = ($spot->author->id == $user->id);
                $userMeetsViewPermission = ($spotIsApproved && $user->can($requiredViewPermission));

                return ($userMeetsViewPermission || $userIsAuthor) && $spotCategoryIsActive;
            }

            return $spotIsApproved && $spotCategoryIsActive && !$requiredViewPermission;
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

    private function validateSentDescriptors(Request $request, Type $type)
    {
        $validatedDescriptors = [];
        $sentDescriptors = collect();
        $requestDescriptors = $request->input('descriptors');
        $categoryRequiredDescriptors = $type->category->descriptors;
        // Loop through all of the sent descriptors to verify they are required by the spots category
        foreach ($requestDescriptors as $descriptorId => $value) {
            // Make sure the descriptor exists
            if ($descriptor = Descriptors::find($descriptorId)) {
                // Ensure the descriptor is required by the category
                if ($categoryRequiredDescriptors->pluck('id')->contains($descriptorId)) {
                    // Verify the value is one of the allowed values
                    if ($descriptor->validate($value)) {
                        $sentDescriptors->push($descriptor);
                        $validatedDescriptors[$descriptorId] = ['value' => $value];
                    } else {
                        $this->validator->errors()->add('Descriptors', "Invalid value, $value, supplied for descriptor $descriptorId");
                    }
                }
            } else {
                $this->validator->errors()->add('Descriptors', "Descriptor $descriptorId does not exist");
            }
        }
        // Compare the descriptors that the category requires with the sent descriptors to make sure no missing required descriptors
        $missingDescriptors = $categoryRequiredDescriptors->diff($sentDescriptors);
        if ($missingDescriptors->count()) {
            $this->validator->errors()->add('Missing Descriptors', $missingDescriptors->pluck('name')->toJson());
        }

        return $validatedDescriptors;
    }

    private function checkUserCanMakeRequestedSpot(User $user, Category $category)
    {
        $crowdsource = $category->crowdsource;
        $canMakeDesignatedSpots = $user->can('make designated spots');

        if (!($crowdsource || $canMakeDesignatedSpots)) {
            $this->validator->errors()->add('Permission Error', 'The category you specified does not allow crowdsourced spots.');
        }

        return $crowdsource ? true : $canMakeDesignatedSpots;
    }

    private function verifyRequestHasRequiredData(Request $request)
    {
        $rules = [
            'notes'             => 'sometimes|string|nullable',
            'image_url'         => 'sometimes|string|nullable',
            'descriptors'       => 'required',
            'type_name'         => 'required',
            'lat'               => 'required|numeric',
            'lng'               => 'required|numeric',
            'classification_id' => 'required|numeric',
        ];
        $this->validator = \Illuminate\Support\Facades\Validator::make(Input::all(), $rules);
    }

    private function getProperClassificationForUser(User $user, Classification $classification)
    {
        if (!$user->can('approve spots')) {
            $newClassification = Category::find($classification->category_id)->underReviewClassification();
            if ($newClassification) {
                $classification = $newClassification;
            } else {
                $this->validator->errors()->add('Internal Error', 'Under review classification does not exist for the given category.');
            }
        }

        return $classification;
    }

    private function validateSentData(Request $request, MessageBag $response)
    {
        $this->verifyRequestHasRequiredData($request);

        if (!($this->validator instanceof Validator)) {
            $response->add('Internal Error', 'Invalid Validator');

            return response($response, 500);
        }

        // Initial validation, just that required fields are sent
        if ($this->validator->fails()) {
            return false;
        }

        $user = $request->user();
        $type = Type::where('name', $request->input('type_name'))->first();
        $approvedClassification = Classification::find($request->input('classification_id'));
        $classification = $this->getProperClassificationForUser($user, $approvedClassification);

        if (!$type || !$classification) {
            $this->validator->errors()->add('Invalid Error', 'You\'ve provided an invalid type or classification.');
        }

        $this->checkUserCanMakeRequestedSpot($user, $type->category);
        $descriptors = $this->validateSentDescriptors($request, $type);

        // Final validation check before spot creation.
        if ($this->validator->fails()) {
            return false;
        }

        return [
            'user'                      => $user,
            'type'                      => $type,
            'descriptors'               => $descriptors,
            'classification'            => $classification,
            'approvedClassification'    => $approvedClassification,
        ];
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
        $response = new MessageBag();
        $validatedData = $this->validateSentData($request, $response);
        if (!$validatedData) {
            return response($this->validator->errors(), 400);
        }

        $user = $validatedData['user'];
        $type = $validatedData['type'];
        $descriptors = $validatedData['descriptors'];
        $classification = $validatedData['classification'];
        $approvedClassification = $validatedData['approvedClassification'];
        $canApproveSpots = $user->can('approve spots');

        $spot = Spot::create([
            'lat'                           => $request->input('lat'),
            'lng'                           => $request->input('lng'),
            'notes'                         => $request->input('notes'),
            'type_id'                       => $type->id,
            'user_id'                       => $user->id,
            'classification_id'             => $classification->id,
            'approved_classification_id'    => $approvedClassification->id,
            'approved'                      => $canApproveSpots ? 1 : 0,
            'image_url'                     => $request->input('image_url'),
        ]);

        if ($spot instanceof Spot) {
            $spot->descriptors()->attach($descriptors);
        }

        $response->add('spot', $spot);
        if ($canApproveSpots) {
            $response->add('message', 'Your spot has been created successfully!');
        } else {
            $response->add('message', "The spot you created will be reviewed and published once approved! Until then hang tight, you'll get an email when your spot has been reviewed.");
        }

        Created::dispatch($spot); // dispatch the Events.Spots.Created event

        return response($response, 201);
    }

    public function getDefaults(Request $request)
    {
        $user = auth('api')->user();
        $categories = Category::forUser($user);

        if ($categoryName = $request->input('category')) {
            $category = Category::where('name', $categoryName)->first();
        } else {
            $category = $categories->first();
        }

        $classifications = Classification::forUser($user);
        $classifications = $classifications->filter(function (Classification $classification) use ($category) {
            return $category ? $classification->category_id == $category->id : false;
        })->values()->all();
        $descriptors = $category ? $category->descriptors : [];
        $types = $category ? $category->types : [];

        $requiredData = [
            'lat'               => 'number',
            'lng'               => 'number',
            'notes'             => 'string',
            'descriptors'       => 'object',
            'type_id'           => 'number',
            'classification_id' => 'number',
        ];

        return [
            'availableTypes'            => $types,
            'availableCategories'       => $categories,
            'availableClassifications'  => $classifications,
            'requiredDescriptors'       => $descriptors,
            'requiredData'              => $requiredData,
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
        $spot->approve();
        Approved::dispatch($spot);
    }

    public function delete(Request $request, Spot $spot)
    {
        try {
            Rejected::dispatch($spot);
            $spot->delete();
            return response('Deletion successful', 200);
        } catch (\Exception $e) {
            Log::error($e);

            return response('Error deleting spot', 500);
        }
    }

    public function stats(Request $request)
    {
        return collect([
            'totalNumberSpots'      => Spot::all()->count(),
            'numberUnapprovedSpots' => Spot::where('approved', false)->count(),
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'image|max:500000',
        ]);

        return $request->file->store('spotImages', ['disk' => 'public']);
    }
}
