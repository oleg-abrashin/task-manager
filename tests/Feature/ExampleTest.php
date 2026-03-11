<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_redirects_to_tasks(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/tasks');
    }
}
