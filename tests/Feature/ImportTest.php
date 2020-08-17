<?php

namespace Tests\Feature;

use App\Spot;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImportTest extends TestCase
{
    protected $importedSpots;

    protected $deletes = ['importedSpots'];

    protected $uriPrefix = '/api/admin/spots/upload';

    private $data = [
        'good' => [
            'spots' => 'testData/spots.csv',
            'descriptors' => 'testData/descriptors.csv',
        ],
        'bad' => [
            'spots' => 'testData/spots-bad.csv',
            'descriptors' => 'testData/descriptors-bad.csv',
        ],
        'common' => [
            'type'                  => 'Couch',
            'author'                => 1,
            'category'              => 'Nap',
            'spotsCsvPath'          => '',
            'classification'        => 1,
            'descriptorsCsvPath'    => '',
        ],
    ];

    public function testTestDataExists()
    {
        foreach ($this->data as $quality => $datum) {
            if ($quality !== 'common') {
                foreach ($datum as $type => $source) {
                    $contents = Storage::get($source);
                    $this->assertNotNull($contents);
                }
            }
        }
    }

    public function testUnauthenticatedSpotsCsvUpload()
    {
        $response = $this->post("$this->uriPrefix/");
        $response->assertStatus(401);
    }

    public function testUnauthorizedSpotsCsvUpload()
    {
        $response = $this->userApi()->post("$this->uriPrefix/");
        $response->assertStatus(403);
    }

    public function testEmptyDataSpotsCsvUpload()
    {
        $response = $this->adminApi()->post("$this->uriPrefix/");
        $response->assertStatus(400);
    }

    public function testUnauthenticatedDescriptorsCsvUpload()
    {
        $response = $this->post("$this->uriPrefix/descriptors");
        $response->assertStatus(401);
    }

    public function testUnauthorizedDescriptorsCsvUpload()
    {
        $response = $this->userApi()->post("$this->uriPrefix/descriptors");
        $response->assertStatus(403);
    }

    public function testEmptyDataDescriptorsCsvUpload()
    {
        $response = $this->adminApi()->post("$this->uriPrefix/descriptors");
        $response->assertStatus(400);
    }

    public function testEmptyDataRun()
    {
        $response = $this->adminApi()->post("$this->uriPrefix/run");
        $response->assertStatus(400);
    }

    public function testBadSpotsRun()
    {
        $data = $this->data['common'];
        $data['spotsCsvPath'] = $this->data['bad']['spots'];
        $data['descriptorsCsvPath'] = $this->data['good']['descriptors'];
        $response = $this->adminApi()->post("$this->uriPrefix/run", $data);
        $response->assertStatus(400);
    }

    public function testBadDescriptorsRun()
    {
        $data = $this->data['common'];
        $data['spotsCsvPath'] = $this->data['good']['spots'];
        $data['descriptorsCsvPath'] = $this->data['bad']['descriptors'];
        $response = $this->adminApi()->post("$this->uriPrefix/run", $data);
        $response->assertStatus(400);
    }

    public function testGoodRun()
    {
        $data = $this->data['common'];
        $data['spotsCsvPath'] = $this->data['good']['spots'];
        $data['descriptorsCsvPath'] = $this->data['good']['descriptors'];
        $response = $this->adminApi()->post("$this->uriPrefix/run", $data);
        $response->assertStatus(200);
        // Set the imported spots to the spots that were just made, so they may
        // be deleted by the test once it's done.
        $spots = collect(json_decode($response->getContent()));
        $this->importedSpots = $spots->map(function ($spotId) {
            return Spot::where('id', $spotId)->first();
        });
    }

}
