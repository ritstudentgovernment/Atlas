<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HomePageTest extends TestCase
{
    /**
     * Test loading the home page
     *
     * @return void
     */
    public function testHomePageLoad()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }
}
