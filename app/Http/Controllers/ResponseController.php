<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use \App\City;
use Response;
use Illuminate\Support\Facades\Validator;


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

            $validated = Validator::make($request->all(), [
                'key' => 'alpha_spaces_comma|Required',
            ]);

        if($validated->passes()) {

            // Split the key string on spaces and commas
            $keywords = preg_split("/[,]+/", $request->input('key'));

            // Use a search that matches the number of splits of the input key
            // This could be one sql query but splitting it may save the database overhead.
            // I haven't tested this theory...if you do let me know the results. 
            switch (count($keywords)) {
                // Search only on the city name
                case 1:
                    $cities = City::where('city_name', 'Like',  $request->input('key') .'%')->take(20)->get();
                    break;
                // Search for the city and subdivision (the state in the USA)
                case 2:
                    // Do we have at least something typed for the subdivision - versus just a space.
                    //if (strlen($keywords[1]) > 0) {
                        // Remove blank spaces...these appear when a user type the comma and surrouding spaces
                        $city = trim($keywords[0]);
                        $subdivision_1 = trim($keywords[1]);
                        \Debugbar::info($subdivision_1);
                        $cities = City::where([
                                        ['city_name', 'Like', $city . '%'],
                                        ['subdivision_1_name', 'Like',  $subdivision_1 . '%'],
                                    ])->take(20)->get();
                        \Debugbar::info($cities);

                    //}
                    break;
                // Search with city, subdivision and country
                case 3:
                    // Do we have at least something typed for the country - versus just  a space.
                    if (strlen($keywords[2]) > 1) {
                        // Remove blank spaces...these appear when a user type the comma and surrouding spaces
                        $city = trim($keywords[0]);
                        $subdivision_1 = trim($keywords[1]);
                        $country = trim($keywords[2]);
                        $cities = City::where([
                                        ['city_name', 'Like', $city . '%'],
                                        ['subdivision_1_name', 'Like',  $subdivision_1 .'%'],
                                        ['country_name', 'Like', $country . '%'],
                                    ])->take(20)->get();

                    } else {
                        $cities = City::where('city_name', 'Like',  $keywords[0] .'%')->take(20)->get();
                    }
                    break;
                default:
                    $cities = City::where('city_name', 'Like',  $keywords[0] .'%')->take(20)->get();
                    break;
            }

            #$cities = City::where('city_name', '=', 'Dallas')->select('city_name', 'country_name')->first();
            #$cities = City::where('city_name', 'Like',  $request->input('key') .'%')->take(20)->get();

            return Response::json($cities)->withCallback($request->input('callback'));
        }
        else {
            \Debugbar::info('Error');
            \Debugbar::info($validated->errors()->all());
            // This is the teapot response - it makes me laugh.
            return Response::json(['error' => $validated->errors()->all()], 418)->withCallback($request->input('callback'));
            // This is the more appropriate response - Unprocessable Entity (due to semantic errors)
            // return Response::json(['error' => $validated->errors()->all()], 422)->withCallback($request->input('callback'));
        }
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
