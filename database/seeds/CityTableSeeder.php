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
        $file = fopen('database/seeds/GeoLite.csv', 'r');
        // Skip the headers (first line)
        $data = fgetcsv($file);
        while (($data = fgetcsv($file)) !== FALSE) {

            DB::table('cities')->insert([
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'geoname_id' => $data[0],
                'locale' => $data[1],
                'continent_code' => $data[2],
                'continent_name' => $data[3],
                'country_iso_code' => $data[4],
                'country_name' => $data[5],
                'subdivision_1_iso_code' => $data[6],
                'subdivision_1_name' => $data[7],
                'subdivision_2_iso_code' => $data[8],
                'subdivision_2_name' => $data[9],
                'city_name' => $data[10],
                'metro_code' => $data[11],
                'time_zone' => $data[12],
            ]);
        }
        fclose($file);
    }
}
