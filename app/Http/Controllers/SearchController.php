<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SearchController extends Controller
{

    // Google Api Key
    public $apiKey = "AIzaSyClKecUuAwqAo5ZNUdQrX_QfyOONjM-Lvo";

    // Search place information from keyword
    public function SearchPlaceGeo(Request $request)
    {
        $url = "https://maps.googleapis.com/maps/api/place/findplacefromtext/json?input=" . $request->search . "&inputtype=textquery&fields=name,geometry&key=" . $this->apiKey;
        $response = Http::get($url);

        // Api request condition
        if ($response->status() === 200) {
            $PlaceGeo = $response->json();

            // Call next function in this class 
            return $this->SearchRestaurant($PlaceGeo);
        } else {
            return response()->json("cant find place", $response->status());
        }
    }

    // Search Restaurant information lists from place information
    public function SearchRestaurant($PlaceGeo)
    {   
        // get latitude of main place
        $lat = $PlaceGeo['candidates'][0]['geometry']['location']['lat'];

        // get longtitude of main place
        $lng = $PlaceGeo['candidates'][0]['geometry']['location']['lng'];

        // format as google required
        $FormatGeo = $lat . ',' . $lng;
        $url = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=" . $FormatGeo . "&radius=5000&type=restaurant&key=" . $this->apiKey;

        // send request to google api
        $response = Http::get($url);

        // main place location
        $responseGeo = $PlaceGeo['candidates'][0]['geometry']['location'];

        // main place word
        $responseWord = $PlaceGeo['candidates'][0];

        // send back to client
        return response()->json(['searchGeo' => $responseGeo, 'data' => $response->json(), 'searchWord' => $responseWord], 200);
    }

    // Get Image Api Function (Not Done)
    public function GetPlaceImage(Request $request)
    {
        if (isset($request->photoRef)) {
            $url = "https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&photoreference=" . $request->photoRef . "&key=" . $this->apiKey;
            $response = Http::get($url);

            return $response;
        } else {
            return response()->json('https://thaigifts.or.th/wp-content/uploads/2017/03/no-image.jpg');
        }
    }
}
