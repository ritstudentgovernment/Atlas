<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class APITest extends TestCase
{

    private $user;
    private $adminUser;

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        parent::setUp();

        $this->user = (factory(User::class))->create();
        $this->adminUser = (factory(User::class))->create();
        if($this->adminUser instanceof User){
            $this->adminUser->assignRole(['admin', 'reviewer']);
        }
    }

    /**
     * Test to make sure that the api returns a response for unauthenticated
     * users on the home page. This response should not include any unapproved spots.
     *
     * @return void
     */
    public function testGetSpotsUnauthenticated()
    {
        $response = $this->get('/api/spots');
        // Make sure the request succeeded
        $response->assertStatus(200);
        // Make sure spots were returned
        $spots = collect($response->decodeResponseJson());
        $this->assertGreaterThan(0, $spots->count());
        // Make sure there are no unapproved spots returned
        $approvals = $spots->pluck('approved');
        $this->assertNotContains(false, $approvals);
    }

    /**
     * Test to make sure that the api returns a response for authenticated (non admin) users on the home page.
     * This response should not include any unapproved spots.
     *
     * @return void
     */
    public function testGetSpotsNonAdmin()
    {
        $response = $this->actingAs($this->user)->get('/api/spots');
        // Make sure the request succeeded
        $response->assertStatus(200);
        // Make sure spots were returned
        $spots = collect($response->decodeResponseJson());
        $this->assertGreaterThan(0, $spots->count());
        // Make sure there are no unapproved spots returned
        $approvals = $spots->pluck('approved');
        $this->assertNotContains(false, $approvals);
    }

    /**
     * Test to make sure that the api returns a response for authenticated (admin) users on the home page.
     * This response should include some unapproved spots.
     *
     * @return void
     */
    public function testGetSpotsAdmin()
    {
        $response = $this->actingAs($this->user)->get('/api/spots');
        // Make sure the request succeeded
        $response->assertStatus(200);
        // Make sure spots were returned
        $spots = collect($response->decodeResponseJson());
        $this->assertGreaterThan(0, $spots->count());
        // Make sure there are unapproved spots returned
        $approvals = $spots->pluck('approved')->toArray();
        $this->assertContains('false', $approvals);
    }
}
