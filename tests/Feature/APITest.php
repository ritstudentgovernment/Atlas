<?php

namespace Tests\Feature;

use \App\User;
use \App\Spot;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class APITest extends TestCase
{

    protected $spot;
    protected $user;
    protected $adminUser;

    protected $deletes = ['spot', 'user', 'adminUser'];

    /**
     * Instantiate the necessary variables to run the tests.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp(); // For some reason this fixed way more than it was supposed to..
        $this->spot = (factory(Spot::class))->create(['approved' => false]);
        $this->user = (factory(User::class))->create();
        $this->adminUser = (factory(User::class))->create();
        if ($this->adminUser instanceof User) {
            $this->adminUser->assignRole(['admin', 'reviewer']);
        }
    }

    public function testPermissions()
    {
        $this->assertFalse($this->user->can('view unapproved spots'));
        $this->assertTrue($this->adminUser->can('view unapproved spots'));
        $this->assertFalse($this->user->can('approve spots'));
        $this->assertTrue($this->adminUser->can('approve spots'));
    }

    /**
     * Test to make sure that the api returns a response for unauthenticated.
     * users on the home page. This response should not include any unapproved spots.
     *
     * @return void
     */
    public function testGetSpotsUnauthenticated()
    {
        $response = $this->get('/api/spots');
        // Make sure the request succeeded.
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
        $response = $this->actingAs($this->adminUser, 'web')->get('/api/spots');
        // Make sure the request succeeded
        $response->assertStatus(200);
        // Make sure spots were returned
        $spots = collect($response->decodeResponseJson());
        $this->assertGreaterThan(0, $spots->count());
        // Make sure there are unapproved spots returned
        $approvals = $spots->pluck('approved');
        $this->assertContains(false, $approvals);
    }

    /**
     * Test to make sure that the api returns a 403 when a non-admin tries to approve a spot
     *
     * @return void
     */
    public function testNonAdminApproveSpot()
    {
        $response = $this->actingAs($this->user)->post('/api/spots/approve/'.$this->spot->id);
        // Make sure the request failed with a 403
        $response->assertStatus(403);
        // Given that the request failed, the spot should still be unapproved, check
        $this->assertFalse(Spot::find($this->spot->id)->approved);
    }

    /**
     * Test to make sure that the api allows an admin to approve a spot
     *
     * @return void
     */
    public function testAdminApproveSpot()
    {
        $response = $this->actingAs($this->adminUser, 'web')->post('/api/spots/approve/'.$this->spot->id);
        // Make sure the request was successful.
        $response->assertStatus(200);
        // Given that the request was successful, find the spot and check to make sure that it is approved.
        $this->assertTrue(Spot::find($this->spot->id)->approved);
    }
}
