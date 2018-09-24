<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Spot;

class SpotTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetApprovedSpots()
    {
        $spots = Spot::where('approved', 1)->get();
        $this->assertTrue($spots->count() > 0);
    }

    public function testGetUnapprovedSpots()
    {
        $spots = Spot::where('approved', 0)->get();
        $this->assertTrue($spots->count() >= 0);
    }
}
