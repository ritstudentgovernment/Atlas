<?php

namespace Tests\Feature;

use App\Type;
use Tests\TestCase;

class TypeAPITest extends TestCase
{
    protected $type;

    protected $deletes = ['type'];

    private $uriPrefix = '/api/admin/spots/type';

    private $newTypeData = [
        'name'      => 'Test',
        'category'  => 1,
    ];

    public function testCreateTypeAdmin()
    {
        $response = $this->adminApi()->post("$this->uriPrefix/create", $this->newTypeData);
        $response->assertStatus(201);
        $this->type = Type::where('name', $this->newTypeData['name'])->first();
    }

    public function testCreateTypeNonAdmin()
    {
        $response = $this->userApi()->post("$this->uriPrefix/create", $this->newTypeData);
        $response->assertStatus(403);
    }

    public function testCreateTypeUnauthenticated()
    {
        $response = $this->post("$this->uriPrefix/create", $this->newTypeData);
        $response->assertStatus(401);
    }

    public function testCreateTypeNoData()
    {
        $response = $this->adminApi()->post("$this->uriPrefix/create", []);
        $response->assertStatus(400);
    }

    public function testCreateTypePartialData()
    {
        $this->newTypeData['name'] = '';
        $response = $this->adminApi()->post("$this->uriPrefix/create", $this->newTypeData);
        $response->assertStatus(400);
    }

    private function makeType()
    {
        $this->newTypeData['category_id'] = $this->newTypeData['category'];
        $this->type = Type::create($this->newTypeData);
        $this->assertNotNull($this->type);

        return $this->type->id;
    }

    public function testDeleteTypeAdmin()
    {
        $type = $this->makeType();
        $response = $this->adminApi()->post("$this->uriPrefix/$type/delete");
        $response->assertStatus(200);
    }

    public function testDeleteTypeNonAdmin()
    {
        $type = $this->makeType();
        $response = $this->userApi()->post("$this->uriPrefix/$type/delete");
        $response->assertStatus(403);
    }

    public function testDeleteTypeUnauthenticated()
    {
        $type = $this->makeType();
        $response = $this->post("$this->uriPrefix/$type/delete");
        $response->assertStatus(401);
    }
}
