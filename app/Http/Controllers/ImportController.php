<?php

namespace App\Http\Controllers;

use App\Category;
use App\Descriptors;
use App\Spot;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use League\Csv\Reader;

class ImportController extends Controller
{
    private $createdSpots;

    /**
     * Constructor to prevent unauthenticated access to sensitive routes.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('role_or_permission:admin|administer');
    }

    public function uploadSpotsCsv(Request $request)
    {
        return $this->uploadCsv($request, 'spots');
    }

    public function uploadDescriptorsCsv(Request $request)
    {
        return $this->uploadCsv($request, 'descriptors');
    }

    private function uploadCsv(Request $request, $spotsOrDescriptors)
    {
        $file = $request->file($spotsOrDescriptors.'Csv');
        if ($file) {
            $path = $file->store("public/csvUploads/$spotsOrDescriptors");

            return $path;
        } else {
            return response("$spotsOrDescriptors CSV file not sent.", 400);
        }
    }

    private function initReader($path)
    {
        $reader = Reader::createFromString(Storage::get($path));
        $reader->setHeaderOffset(0);

        return $reader;
    }

    private function hasRequiredSpotData($data)
    {
        $validatedData = [];
        $requiredKeys = ['lat', 'lng', 'notes'];
        foreach ($requiredKeys as $requiredKey) {
            // Make sure the required data is being sent with each spot. Notes can be null.
            if (array_key_exists($requiredKey, $data) && ($data[$requiredKey] || $requiredKey === 'notes')) {
                $validatedData[$requiredKey] = $data[$requiredKey];
            } else {
                return false;
            }
        }

        return $validatedData;
    }

    private function importSpotsFromCsv($csvPath, $commonData)
    {
        $spots = collect();
        $spotsCsv = $this->initReader($csvPath);

        foreach ($spotsCsv as $csvLine) {
            $validatedSpotData = $this->hasRequiredSpotData($csvLine);
            if ($validatedSpotData) {
                $spotData = array_merge($commonData, $validatedSpotData);
                $spots->push(Spot::create($spotData));
            } else {
                $this->createdSpots = $spots;
                return false;
            }
        }

        $this->createdSpots = $spots;
        return true;
    }

    private function importDescriptorCsvLine($csvLine, $requiredDescriptors)
    {
        $descriptors = [];

        foreach ($csvLine as $descriptorName => $value) {
            // Verify that the descriptor being sent is one of the required descriptors
            $descriptor = $requiredDescriptors->filter(function (Descriptors $descriptor) use ($descriptorName) {
                return $descriptor->name == $descriptorName;
            })->first();

            if ($descriptor === null || $value === null) {
                return false;
            }

            $descriptors[$descriptor->id] = ['value' => $value];
        }

        return $descriptors;
    }

    private function importDescriptorsFromCsv($csvPath)
    {
        $descriptorsCsv = $this->initReader($csvPath);

        foreach ($descriptorsCsv as $index => $csvLine) {
            $spot = $this->createdSpots->get($index - 1);
            $requiredDescriptors = $spot->category->descriptors;
            $descriptors = $this->importDescriptorCsvLine($csvLine, $requiredDescriptors);

            if ($descriptors) {
                $spot->descriptors()->attach($descriptors);
            } else {
                return false;
            }
        }

        return true;
    }

    private function importValidator(Request $request)
    {
        $rules = [
            'type'                  => 'required|string',
            'author'                => 'required|integer',
            'category'              => 'required|string',
            'spotsCsvPath'          => 'required|string',
            'classification'        => 'required|integer',
            'descriptorsCsvPath'    => 'required|string',
        ];

        return Validator::make($request->all(), $rules);
    }

    private function commonData(Request $request, Category $category)
    {
        return [
            'approved'          => true,
            'user_id'           => $request->input('author'),
            'classification_id' => $request->input('classification'),
            'type_id'           => $category->types()->where('name', $request->input('type'))->first()->id,
        ];
    }

    private function deleteCreatedSpots()
    {
        if ($this->createdSpots && $this->createdSpots instanceof Collection) {
            $this->createdSpots->each(function (Spot $spot) {
                $spot->delete();
                $spot->forceDelete();
            });
        }
    }

    public function runImport(Request $request)
    {
        $validator = $this->importValidator($request);

        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }

        $category = Category::where('name', $request->input('category'))->first();

        if ($category === null) {
            return response('Category not found', 400);
        }

        $commonData = $this->commonData($request, $category);

        if ($this->importSpotsFromCsv($request->input('spotsCsvPath'), $commonData)) {
            if ($this->importDescriptorsFromCsv($request->input('descriptorsCsvPath'))) {
                $spots = $this->createdSpots->map(function (Spot $spot) {
                    return $spot->id;
                });
                return response($spots, 200);
            }

            $this->deleteCreatedSpots();
            return response('Failed To Import Descriptors', 400);
        }

        $this->deleteCreatedSpots();
        return response('Failed To Import Spots', 400);
    }
}
