<?php

use Illuminate\Database\Seeder;

class CountryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = file_get_contents(__DIR__ . '/country_cities.json');

        $country = json_decode($json, true);
        foreach ($country as $key => $cities) {
            DB::table('countries')->insert([
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'country' => $key,
            ]);

        }
    }
}
