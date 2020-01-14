<?php

namespace Tests\Feature;

use App\Category;
use Tests\TestCase;

class CategoryAPITest extends TestCase
{
    protected $category;

    protected $deletes = ['category'];

    private $uriPrefix = '/api/admin/spots';

    private $newCategoryData = [
        'name'          => 'Test',
        'icon'          => 'T',
        'description'   => 'This is a test, don\'t expect much!',
    ];

    public function testCreateCategoryAdmin()
    {
        $response = $this->adminApi()->post("$this->uriPrefix/category/create", $this->newCategoryData);
        $response->assertStatus(201);
    }

    public function testCreateCategoryNonAdmin()
    {
        $response = $this->userApi()->post("$this->uriPrefix/category/create", $this->newCategoryData);
        $response->assertStatus(403);
    }

    public function testCreateCategoryUnauthenticated()
    {
        $response = $this->post("$this->uriPrefix/category/create", $this->newCategoryData);
        $response->assertStatus(401);
    }

    public function testCreateCategoryNoData()
    {
        $response = $this->adminApi()->post("$this->uriPrefix/category/create", []);
        $response->assertStatus(400);
    }

    public function testCreateCategoryPartialData()
    {
        $this->newCategoryData['icon'] = '';
        $response = $this->adminApi()->post("$this->uriPrefix/category/create", $this->newCategoryData);
        $response->assertStatus(400);
    }

    private function makeCategory()
    {
        $this->category = Category::create($this->newCategoryData);
        $this->assertNotNull($this->category);

        return $this->category->name;
    }

    public function testDeleteCategoryAdmin()
    {
        $name = $this->makeCategory();
        $response = $this->adminApi()->post("$this->uriPrefix/category/$name/delete");
        $response->assertStatus(200);
    }

    public function testDeleteCategoryNonAdmin()
    {
        $name = $this->makeCategory();
        $response = $this->userApi()->post("$this->uriPrefix/category/$name/delete");
        $response->assertStatus(403);
    }

    public function testDeleteCategoryUnauthenticated()
    {
        $name = $this->makeCategory();
        $response = $this->post("$this->uriPrefix/category/$name/delete");
        $response->assertStatus(401);
    }

    public function testUpdateCategoryAdmin()
    {
        $name = $this->makeCategory();
        $response = $this->adminApi()->post("$this->uriPrefix/category/$name/update", ['icon' => 'U']);
        $response->assertStatus(200);
        $this->category = Category::find($this->category->id);
        $this->assertEquals('U', $this->category->icon);
    }

    public function testUpdateCategoryNonAdmin()
    {
        $name = $this->makeCategory();
        $response = $this->userApi()->post("$this->uriPrefix/category/$name/update", ['icon' => 'U']);
        $response->assertStatus(403);
        $this->category = Category::find($this->category->id);
        $this->assertEquals('T', $this->category->icon);
    }

    public function testUpdateCategoryUnauthenticated()
    {
        $name = $this->makeCategory();
        $response = $this->post("$this->uriPrefix/category/$name/update", ['icon' => 'U']);
        $response->assertStatus(401);
        $this->category = Category::find($this->category->id);
        $this->assertEquals('T', $this->category->icon);
    }

    public function testUpdateCategoryNoData()
    {
        $name = $this->makeCategory();
        $response = $this->adminApi()->post("$this->uriPrefix/category/$name/update", []);
        $response->assertStatus(400);
    }

    public function testUpdateCategoryNotAllowedData()
    {
        $name = $this->makeCategory();
        $response = $this->adminApi()->post("$this->uriPrefix/category/$name/update", ['id' => '100']);
        $response->assertStatus(400);
    }
}
