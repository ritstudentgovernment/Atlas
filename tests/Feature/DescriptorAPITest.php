<?php

namespace Tests\Feature;

use App\Descriptors;
use Tests\TestCase;

class DescriptorAPITest extends TestCase
{
    protected $descriptor;

    protected $deletes = ['descriptor'];

    private $uriPrefix = '/api/admin/spots/descriptor';

    private $newDescriptorData = [
        'name'              => 'Test',
        'value_type'        => 'Select',
        'default_value'     => 'Nope',
        'allowed_values'    => 'Yup|Nope',
        'icon'              => 'rss',
    ];

    public function testCreateDescriptorAdmin()
    {
        $response = $this->adminApi()->post("$this->uriPrefix/create", $this->newDescriptorData);
        $response->assertStatus(201);
        $this->descriptor = Descriptors::where('name', $this->newDescriptorData['name'])->first();
    }

    public function testCreateDescriptorNonAdmin()
    {
        $response = $this->userApi()->post("$this->uriPrefix/create", $this->newDescriptorData);
        $response->assertStatus(403);
    }

    public function testCreateDescriptorUnauthenticated()
    {
        $response = $this->post("$this->uriPrefix/create", $this->newDescriptorData);
        $response->assertStatus(401);
    }

    public function testCreateDescriptorNoData()
    {
        $response = $this->adminApi()->post("$this->uriPrefix/create", []);
        $response->assertStatus(400);
    }

    public function testCreateDescriptorPartialData()
    {
        $this->newDescriptorData['name'] = '';
        $response = $this->adminApi()->post("$this->uriPrefix/create", $this->newDescriptorData);
        $response->assertStatus(400);
    }

    private function makeDescriptor()
    {
        $this->descriptor = Descriptors::create($this->newDescriptorData);
        $this->assertNotNull($this->descriptor);

        return $this->descriptor->id;
    }

    public function testDeleteDescriptorAdmin()
    {
        $descriptor = $this->makeDescriptor();
        $response = $this->adminApi()->post("$this->uriPrefix/$descriptor/delete");
        $response->assertStatus(200);
    }

    public function testDeleteDescriptorNonAdmin()
    {
        $descriptor = $this->makeDescriptor();
        $response = $this->userApi()->post("$this->uriPrefix/$descriptor/delete");
        $response->assertStatus(403);
    }

    public function testDeleteDescriptorUnauthenticated()
    {
        $descriptor = $this->makeDescriptor();
        $response = $this->post("$this->uriPrefix/$descriptor/delete");
        $response->assertStatus(401);
    }

    public function testUpdateDescriptorAdmin()
    {
        $descriptor = $this->makeDescriptor();
        $response = $this->adminApi()->post("$this->uriPrefix/$descriptor/update", ['icon' => 'happy']);
        $response->assertStatus(200);
        $this->descriptor = Descriptors::find($this->descriptor->id);
        $this->assertEquals('happy', $this->descriptor->icon);
    }

    public function testUpdateDescriptorNonAdmin()
    {
        $descriptor = $this->makeDescriptor();
        $response = $this->userApi()->post("$this->uriPrefix/$descriptor/update", ['icon' => 'happy']);
        $response->assertStatus(403);
        $this->descriptor = Descriptors::find($this->descriptor->id);
        $this->assertEquals('rss', $this->descriptor->icon);
    }

    public function testUpdateDescriptorUnauthenticated()
    {
        $descriptor = $this->makeDescriptor();
        $response = $this->post("$this->uriPrefix/$descriptor/update", ['icon' => 'happy']);
        $response->assertStatus(401);
        $this->descriptor = Descriptors::find($this->descriptor->id);
        $this->assertEquals('rss', $this->descriptor->icon);
    }

    public function testUpdateDescriptorNoData()
    {
        $descriptor = $this->makeDescriptor();
        $response = $this->adminApi()->post("$this->uriPrefix/$descriptor/update", []);
        $response->assertStatus(400);
    }

    public function testUpdateDescriptorNotAllowedData()
    {
        $descriptor = $this->makeDescriptor();
        $response = $this->adminApi()->post("$this->uriPrefix/$descriptor/update", ['id' => '100']);
        $response->assertStatus(400);
    }
}
