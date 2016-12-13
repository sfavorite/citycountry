<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class ResponseController extends Controller
{
    # Try to determine the query type (zip, city name, location (eiffel tower))
    private function queryType($request) {
        return 'nonya';
    }

    // Return list of all cities in a given country
    function getCities(Request $request) {

        $this->validate($request, [
            'key' => 'AlphaNum|Required',
        ]);

        //$cities = \App\City::where('user_id', '=', $user_id)->addSelect(array('id', 'city'))->get();

        // Return normal json
        $cities = \App\City::where('city', 'LIKE', $request->input('key') . '%')->addSelect(array('city'))->get();
        //$cities = \App\City::where('city', 'LIKE', 'Cuy%')->addSelect(array('city'))->get();
        return $cities;

        // Return jsonp
        return response()
                    ->json($latLong)
                    ->withCallback($request->input('callback'));
    }

    function getCountries(Request $request) {

                $this->validate($request, [
                    'key' => 'AlphaNum|Required',
                ]);

                //$cities = \App\City::where('user_id', '=', $user_id)->addSelect(array('id', 'city'))->get();

                $cities = \App\Country::where('country', 'LIKE', $request->input('key') . '%')->addSelect(array('country'))->get();
                return $cities;
    }

    function getCityCountry(Request $request) {

        Log::info('Function getCityCountry');
        $key = $request->key . "%";

        Log::info('Key = ' . $key);
        $results = DB::select("select cities.city, countries.country from countries inner join (cities inner join city_country on cities.id = city_country.city_id) on countries.id = city_country.country_id where cities.city like ? LIMIT 25;", [$key]);

        // Return jsonp
        return response()
                    ->json($results)
                    ->withCallback($request->input('callback'));

    }

    function getLatLong(Request $request) {
        $map_key = env('BING_MAP_KEY');

        $this->validate($request, [
            'key' => 'AlphaNum|Required'
        ]);

        // For now only check for city name
        $queryType = 'city';

        # Start a Guzzle client
        $client = new Client();
        $city = $request->city;
        # Get query type
        switch($queryType) {
            case 'city':
                $res = $client->request('GET', 'http://dev.virtualearth.net/REST/v1/Locations?locality=' . $city . '&key=' . $map_key);
                break;
            case 'citystate':
                $res = $client->request('GET', 'http://dev.virtualearth.net/REST/v1/Locations/US/MN//New%20York%20Mills/?o=json&key=A' . $map_key);
                break;
            case 'zip':
                $res = $client->request('GET', 'http://dev.virtualearth.net/REST/v1/Locations?postalCode=44223&key=' . $map_key);
                break;
            default:
                $res = $client->request('GET', 'http://dev.virtualearth.net/REST/v1/Locations?postalCode=44223&key=' . $map_key);
        }

        $bingJson = $res->getBody();
        $bingObject = json_decode($bingJson, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            //return $bingObject['resourceSets'][0]['resources'][0]['point']['coordinates'];
            $latLong = $bingObject['resourceSets'][0]['resources'][0]['point']['coordinates'];
            // Return jsonp
            return response()
                        ->json($latLong)
                        ->withCallback($request->input('callback'));

        } else {
            return 'JSON Error';
        }
        //http://dev.virtualearth.net/REST/v1/Locations/US/MN//New York Mills/?o=json&key=Ao5xGJzWOhTG6bx3Ea4cCBrERITo53x03OAHsE9mSJKGRkQ6-Xlvk-H_RNGCf9rq


    }
}
