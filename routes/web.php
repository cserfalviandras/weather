<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherController;

Route::get('/weather/{city}', [WeatherController::class, 'show']);

Route::get('/weather/{city}/getWeather', [WeatherController::class, 'getCityWeatherData']);

Route::get('/weather/{city}/detailedForecast', [WeatherController::class, 'getDetailedForecast']);
