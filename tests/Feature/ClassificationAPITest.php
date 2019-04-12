<?php

namespace Tests\Feature;

use App\Classification;
use Tests\TestCase;

class ClassificationAPITest extends TestCase
{
    protected $classification;

    protected $deletes = ['classification'];

    private $uriPrefix = '/api/admin/spots/classification';

    private $newClassificationData = [
        'category_id'       => 1,
        'create_permission' => '',
        'view_permission'   => '',
        'color'             => '#ffffff',
        'name'              => 'Test',
        'type'              => 'test',
    ];

    public function testCreateClassificationAdmin()
    {
        $response = $this->adminApi()->post("$this->uriPrefix/create", $this->newClassificationData);
        $response->assertStatus(201);
        $this->classification = Classification::where('name', $this->newClassificationData['name'])->first();
    }

    public function testCreateClassificationNonAdmin()
    {
        $response = $this->userApi()->post("$this->uriPrefix/create", $this->newClassificationData);
        $response->assertStatus(403);
    }

    public function testCreateClassificationUnauthenticated()
    {
        $response = $this->post("$this->uriPrefix/create", $this->newClassificationData);
        $response->assertStatus(401);
    }

    public function testCreateClassificationNoData()
    {
        $response = $this->adminApi()->post("$this->uriPrefix/create", []);
        $response->assertStatus(400);
    }

    public function testCreateClassificationPartialData()
    {
        $this->newClassificationData['name'] = '';
        $response = $this->adminApi()->post("$this->uriPrefix/create", $this->newClassificationData);
        $response->assertStatus(400);
    }

    private function makeClassification()
    {
        $this->classification = Classification::create($this->newClassificationData);
        $this->assertNotNull($this->classification);
        return $this->classification->id;
    }

    public function testDeleteClassificationAdmin()
    {
        $classification = $this->makeClassification();
        $response = $this->adminApi()->post("$this->uriPrefix/$classification/delete");
        $response->assertStatus(200);
    }

    public function testDeleteClassificationNonAdmin()
    {
        $classification = $this->makeClassification();
        $response = $this->userApi()->post("$this->uriPrefix/$classification/delete");
        $response->assertStatus(403);
    }

    public function testDeleteClassificationUnauthenticated()
    {
        $classification = $this->makeClassification();
        $response = $this->post("$this->uriPrefix/$classification/delete");
        $response->assertStatus(401);
    }

    public function testSoftDeleteClassificationAdmin()
    {
        $classification = $this->makeClassification();
        $response = $this->adminApi()->post("$this->uriPrefix/$classification/delete/soft");
        $response->assertStatus(200);
        $this->classification = Classification::find($this->classification->id);
        $this->assertTrue($this->classification->deleted);
    }

    public function testSoftDeleteClassificationNonAdmin()
    {
        $classification = $this->makeClassification();
        $response = $this->userApi()->post("$this->uriPrefix/$classification/delete/soft");
        $response->assertStatus(403);
        $this->classification = Classification::where('name', $this->classification->name)->first();
        $this->assertFalse($this->classification->deleted);
    }

    public function testSoftDeleteClassificationUnauthenticated()
    {
        $classification = $this->makeClassification();
        $response = $this->post("$this->uriPrefix/$classification/delete/soft");
        $response->assertStatus(401);
        $this->classification = Classification::where('name', $this->classification->name)->first();
        $this->assertFalse($this->classification->deleted);
    }

    public function testUpdateClassificationAdmin()
    {
        $classification = $this->makeClassification();
        $response = $this->adminApi()->post("$this->uriPrefix/$classification/update", ['color' => '#000000']);
        $response->assertStatus(200);
        $this->classification = Classification::find($this->classification->id);
        $this->assertEquals('#000000', $this->classification->color);
    }

    public function testUpdateClassificationNonAdmin()
    {
        $classification = $this->makeClassification();
        $response = $this->userApi()->post("$this->uriPrefix/$classification/update", ['color' => '#000000']);
        $response->assertStatus(403);
        $this->classification = Classification::find($this->classification->id);
        $this->assertEquals('#ffffff', $this->classification->color);
    }

    public function testUpdateClassificationUnauthenticated()
    {
        $classification = $this->makeClassification();
        $response = $this->post("$this->uriPrefix/$classification/update", ['color' => '#000000']);
        $response->assertStatus(401);
        $this->classification = Classification::find($this->classification->id);
        $this->assertEquals('#ffffff', $this->classification->color);
    }

    public function testUpdateClassificationNoData()
    {
        $classification = $this->makeClassification();
        $response = $this->adminApi()->post("$this->uriPrefix/$classification/update", []);
        $response->assertStatus(400);
    }

    public function testUpdateClassificationNotAllowedData()
    {
        $classification = $this->makeClassification();
        $response = $this->adminApi()->post("$this->uriPrefix/$classification/update", ['id' => '100']);
        $response->assertStatus(400);
    }
}
