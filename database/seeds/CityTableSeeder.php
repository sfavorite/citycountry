<?php

use Illuminate\Database\Seeder;

class CityTableSeeder extends Seeder
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
        $city_array = array();
        echo 'Starting loop....this will take a while' . "\r\n";

        $country_count = 0;
        $total_cities = 0;
        $time_start = microtime(true);

        $DB_countries = \App\Country::get();
        foreach ($country as $key => $cities) {
            $A_country = $DB_countries->filter(function($item) use($key){
                return $item->country === $key;
            })->first();

            $country_count += 1;

            // Get the country from the database
            echo $country_count . " of 247) " . $key. "\r\n";

            $city_count = 0;
            foreach($cities as $city) {
                $city_count += 1;
                $total_cities += 1;

                if (!in_array($city, $city_array)) {
                    $city_array[] = $city;

                    $db_city = new \App\City();
                    $db_city->city = $city;
                    $db_city->save();
                } else {
                    // Get the city from the database;
                    $db_city = \App\City::where('city', '=', $city)->first();
                }

                // Make our pivot table association.
                $A_country->cities()->save($db_city);
            }
            echo 'Number of cities: ' . $city_count . "\r\n";
        }
        echo 'Total cities processed: ' . $total_cities . "\r\n";
        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start)/60;
        echo 'Total execution time: ' . $execution_time . "\r\n";

    }
}
