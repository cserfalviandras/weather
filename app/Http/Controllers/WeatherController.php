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
        $url = config('weather-api.base-url').'/city-weather/'.$city;

        return $this->getData($url);
    }

    public function getDetailedForecast($city) 
    {
        $cityCurrentWeatherData = $this->getCityWeatherData($city);
        $lat = $cityCurrentWeatherData['coord']['lat'];
        $lon = $cityCurrentWeatherData['coord']['lon'];

        $url = config('weather-api.base-url').'/detailed-forecast/'.$lat.'/'.$lon;
        $detailedForecastData = $this->getData($url);

        return array_merge($cityCurrentWeatherData, $detailedForecastData);
    }

    private function getData($url)
    {
        $response = Http::get($url);

        return $response->json();
    }
}
