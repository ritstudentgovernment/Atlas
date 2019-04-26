<?php

namespace App\Http\Controllers;

use App\Category;
use App\Descriptors;
use App\Spot;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
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
        $path = $request->file($spotsOrDescriptors.'Csv')->store("public/csvUploads/$spotsOrDescriptors");

        return $path;
    }

    private function initReader($path){
        $reader = Reader::createFromString(Storage::get($path));
        $reader->setHeaderOffset(0);
        return $reader;
    }

    private function importSpotsFromCsv($csvPath, $commonData)
    {
        $spots = collect([]);
        $spotsCsv = $this->initReader($csvPath);

        foreach ($spotsCsv as $csvLine) {

            $spotData = array_merge($commonData, [
                'lat'   => $csvLine['lat'],
                'lng'   => $csvLine['lng'],
                'notes' => $csvLine['notes'],
            ]);

            $spots->push(Spot::create($spotData));

        }

        return $spots;
    }

    private function importDescriptorsFromCsv($csvPath, $spots)
    {
        $descriptorsCsv = $this->initReader($csvPath);

        foreach ($descriptorsCsv as $index => $csvLine) {

            $spot = $spots->get($index);
            $descriptors = [];
            $requiredDescriptors = $spot->category->descriptors;

            foreach ($csvLine as $descriptorName => $value) {

                $descriptor = $requiredDescriptors->filter(function (Descriptors $descriptor) use ($descriptorName) {
                    return $descriptor->name == $descriptorName;
                })->first();

                if ($descriptor === null) {
                    return false;
                }

                $descriptors[$descriptor->id] = ['value' => $value];

            }

            $spot->descriptors()->attach($descriptors);

        }

        return $spots;
    }

    public function runImport(Request $request)
    {
        $rules = [
            'type'                  => 'required|string',
            'author'                => 'required|integer',
            'category'              => 'required|string',
            'spotsCsvPath'          => 'required|string',
            'classification'        => 'required|integer',
            'descriptorsCsvPath'    => 'required|string',
        ];
        $validator = \Illuminate\Support\Facades\Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }

        $category = Category::where('name', $request->input('category'))->first();

        if ($category === null) {
            return response("Category not found", 400);
        }

        $commonData = [
            'approved'          => true,
            'user_id'           => $request->input('author'),
            'classification_id' => $request->input('classification'),
            'type_id'           => $category->types()->where('name', $request->input('type'))->first()->id,
        ];

        if ($spots = $this->importSpotsFromCsv($request->input('spotsCsvPath'), $commonData)) {

            if ($this->importDescriptorsFromCsv($request->input('descriptorsCsvPath'), $spots)) {

                return response('Spots Import Success', 200);

            }

            return response('Failed To Import Descriptors', 400);
        }

        return response('Failed To Import Spots', 400);
    }

}
