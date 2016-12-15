<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DatabaseConnected extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->seeInDatabase('geolite', [
            'city_name' => 'Toledo'
        ]);
        $this->seeInDatabase('geolite', [
            'country_name' => 'Cyprus'
        ]);

    }
}
