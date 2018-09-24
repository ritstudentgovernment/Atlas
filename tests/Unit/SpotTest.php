<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Spot;

class SpotTest extends TestCase
{
    /**
     * Basic test to make sure there are approved spots in the db
     *
     * @return void
     */
    public function testGetApprovedSpots()
    {
        $spots = Spot::where('approved', 1)->get();
        // Make sure there are at least one approved spot in the database
        $this->assertTrue($spots->count() > 0);
    }

    /**
     * Basic test to make sure there are unapproved spots in the db
     *
     * @return void
     */
    public function testGetUnapprovedSpots()
    {
        $spots = Spot::where('approved', 0)->get();
        // Make sure there are at least one unapproved spot in the database
        $this->assertTrue($spots->count() > 0);
    }
}
