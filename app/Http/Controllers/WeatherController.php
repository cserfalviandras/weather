<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function show(Request $request, $city)
    {
        return view('weather.show', [
                'city' => $city
            ]);
    }

    public function getCityWeatherData($city) 
    {
        $url = 'https://weather-api-service.herokuapp.com/city-weather/'.$city;

        $response = Http::get($url);

        return $response->json();
    }
}
