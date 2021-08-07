<?php

use Illuminate\Support\Facades\Route;
use Zttp\Zttp;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('weather', function () {
    $openWeatherApiKey = config('services.open_weather.key');

    $lat = request('lat');
    $lon = request('lon');

    $response = Zttp::get("https://api.openweathermap.org/data/2.5/onecall?lat=$lat&lon=$lon&exclude=hourly,minutely&appid=$openWeatherApiKey");

    return $response->json();
});
