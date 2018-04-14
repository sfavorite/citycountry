<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use \App\City;
use Response;

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

    function getCityInfo(Request $request) {

        //$cities = City::where('city_name', '=', 'Dallas')->select('city_name', 'country_name')->first();
        $cities = City::where('city_name', 'Like',  $request->key .'%')->take(20)->get();
        return 'Key: ' . $request->key;
        \Debugbar::info($cities);
        return Response::json($cities)->withCallback($request->input('callback'));

    }

    function getCityCountry(Request $request) {


        Log::info('Function getCityCountry');
        $key = $request->key . "%";

        Log::info($key);
        $results = DB::select("select cities.city, countries.country from countries inner join (cities inner join city_country on cities.id = city_country.city_id) on countries.id = city_country.country_id where cities.city like ? LIMIT 25;", [$key]);
        Log::info($results);
        // Return jsonp
        return response()
                    ->json($cities)
                    ->withCallback($request->input('callback'));

    }

    function getLatLon(Request $request) {
        $map_key = env('BING_MAP_KEY');


        // For now only check for city name
        $queryType = 'citycountrydistrict';


        $url = 'http://dev.virtualearth.net/REST/v1/Locations/' . $request->country . '/' . $request->subdivision1 . '/' . $request->city . '/?o=json&key=' . $map_key;
        //$url = 'http://dev.virtualearth.net/REST/v1/Locations/' . $request->country . '/' . $request->city . '?o=json&key=' . $map_key;

        # Start a Guzzle client
        $client = new Client();

        # Get query type
        switch($queryType) {
            case 'city':
                $res = $client->request('GET', 'http://dev.virtualearth.net/REST/v1/Locations/' . $request->city . '?o=json&key=' . $map_key);
                break;
            case 'citycountrydistrict':
                \Debugbar::info($url);
                //$res = $client->request('GET', 'http://dev.virtualearth.net/REST/v1/Locations/Belize/Belize%20District/Belize%20City/?o=json&key=' . $map_key);
                $res = $client->request('GET', $url);
                break;
            case 'zip':
                $res = $client->request('GET', 'http://dev.virtualearth.net/REST/v1/Locations?postalCode=44223&key=' . $map_key);
                break;
            default:
                $res = $client->request('GET', 'http://dev.virtualearth.net/REST/v1/Locations?postalCode=44223&key=' . $map_key);
        }

        $bingJson = $res->getBody();
        $bingObject = json_decode($bingJson, true);
        \Debugbar::info($bingObject);
        if (json_last_error() === JSON_ERROR_NONE) {
            if (count($bingObject['resourceSets'][0]['resources'])) {
                $latLong = $bingObject['resourceSets'][0]['resources'][0]['point']['coordinates'];
                return response()
                            ->json($latLong)
                            ->withCallback($request->input('callback'));

            } else {
                return 'No location information';
            }

        } else {
            return 'JSON Error';
        }


    }
}
