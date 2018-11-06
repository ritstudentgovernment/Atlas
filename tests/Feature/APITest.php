<?php

namespace Tests\Feature;

use App\Spot;
use App\Type;
use App\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class APITest extends TestCase
{
    protected $spot;
    protected $user;
    protected $adminUser;
    protected $newSpotData;

    protected $deletes = ['spot'];

    /**
     * Instantiate the necessary variables to run the tests.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp(); // For some reason this fixed way more than it was supposed to..
        $this->spot = (factory(Spot::class))->create(['approved' => false]);
        $this->user = User::where('first_name', 'Morty')->first();
        $this->adminUser = User::where('first_name', 'Sheldon')->first();
        $this->newSpotData = [
            'title'     => 'TEST',
            'quietLevel'=> 10,
            'notes'     => 'this is a test spot, dont expect much',
            'type_id'   => Type::first() ? Type::inRandomOrder()->first()->id : null,
            'lat'       => env('GOOGLE_MAPS_CENTER_LAT'),
            'lng'       => env('GOOGLE_MAPS_CENTER_LNG')
        ];
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
     * This response should not include any unapproved spots that the user did not author.
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
        // Filter out spots the user authored, because they may not be approved yet
        $spots = $spots->filter(function ($value) {
            return $value['authored'] != true;
        });
        // Make sure there are no unapproved spots returned
        $approvals = $spots->pluck('approved');
        $this->assertNotContains(false, $approvals->toArray());
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
        $this->assertContains(false, $approvals->toArray());
    }

    /**
     * Test to make sure that the api denies an unauthenticated request to make a new spot.
     *
     * @return void
     */
    public function testCreateSpotUnauthenticated()
    {
        $response = $this->post('/api/spots/create', $this->newSpotData);
        $response->assertStatus(401);
    }

    /**
     * Test to make sure that the api returns a 403 when a non-admin tries to approve a spot.
     *
     * @return void
     */
    public function testNonAdminApproveSpot()
    {
        $response = $this->actingAs($this->user, 'api')->post('/api/spots/approve/'.$this->spot->id);
        // Make sure the request failed with a 403
        $response->assertStatus(403);
        // Given that the request failed, the spot should still be unapproved, check
        $this->assertFalse(Spot::find($this->spot->id)->approved);
    }

    /**
     * Test to make sure that the api allows an admin to approve a spot.
     *
     * @return void
     */
    public function testAdminApproveSpot()
    {
        $response = $this->actingAs($this->adminUser, 'api')->post('/api/spots/approve/'.$this->spot->id);
        // Make sure the request was successful.
        $response->assertStatus(200);
        // Given that the request was successful, find the spot and check to make sure that it is approved.
        $this->assertTrue(Spot::find($this->spot->id)->approved);
    }

}
