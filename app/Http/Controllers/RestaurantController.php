<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function getByWord(Request $request, $placesText) {
        $nextPageToken=$request->input('nextpagetoken');

        // generate Google Place API
        $url = 'https://maps.googleapis.com/maps/api/place/textsearch/json';

        if (!isset($nextPageToken) || trim($nextPageToken) == '') { // first page
            $url .= '?query=' . $placesText;
            $url .= '&language=th';
            $url .= '$region=.th'; // region must be thai only
            $url .= '&type=restaurant'; // region must be restaurant only
            // $url .= '&inputtype=textquery&fields=photos,formatted_address,name,rating,opening_hours,geometry,types,icon';
            $url .= '&inputtype=textquery&fields=rating,opening_hours';
        } else { // next page
            $url .= '?pagetoken=' . $nextPageToken;
        }

        $url .= '&key=' . env("GOOGLE_PLACE_API_KEY", "");


        $response = Http::get($url);

        // if have error status from extrenal API
        // this API will reject 500 only
        if (!$response->successful()) {
            Log::error($response);
            return abort('500', $response);
        }

        return response($response)
            ->header('Content-Type', 'application/json');
    }
}
