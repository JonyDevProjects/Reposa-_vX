<?php

namespace Tests;

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.mysql.database' => 'reposaplus_testing']);
        $this->withoutMiddleware(PreventRequestForgery::class);
        $this->withoutVite();
    }
}
