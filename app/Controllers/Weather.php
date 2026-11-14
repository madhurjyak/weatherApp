<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\WeatherService;

class Weather extends BaseController
{
    /**
     * Display the homepage with the search form.
     */
    public function index()
    {
        $data = [
            'title' => 'Weather App - Live Forecasts',
        ];

        return view('weather/index', $data);
    }

    /**
     * Handle the city search with validation.
     */
    public function search()
    {
        $cityName = trim($this->request->getGet('city') ?? '');

        // Validation Rules using CodeIgniter's validator
        $validationRules = [
            'city' => [
                'rules'  => 'required|min_length[2]|max_length[100]',
                'errors' => [
                    'required'   => 'Please enter a city name to search.',
                    'min_length' => 'City name must have at least 2 characters.',
                    'max_length' => 'City name is too long (maximum 100 characters).',
                ],
            ],
        ];

        if (! $this->validateData(['city' => $cityName], $validationRules)) {
            // Grab the first validation error message
            $errorMessage = $this->validator->getError('city');
            return redirect()->to('/')->with('error', $errorMessage);
        }

        // Call the Service to query the Geocoding API
        $weatherService = new WeatherService();
        $cities = $weatherService->searchCity($cityName);

        $data = [
            'title'  => "Search Results for \"{$cityName}\"",
            'city'   => $cityName,
            'cities' => $cities,
        ];

        return view('weather/search_results', $data);
    }

    /**
     * Display weather dashboard with coordinate boundary and validity checks.
     */
    public function forecast()
    {
        $latRaw  = $this->request->getGet('lat');
        $lonRaw  = $this->request->getGet('lon');
        $name    = trim($this->request->getGet('name') ?? 'Unknown Location');
        $country = trim($this->request->getGet('country') ?? '');

        // 1. Check if parameters are missing or non-numeric
        if ($latRaw === null || $lonRaw === null || ! is_numeric($latRaw) || ! is_numeric($lonRaw)) {
            return redirect()->to('/')->with('error', 'Invalid or missing geographical coordinates. Please search for a city.');
        }

        $lat = (float) $latRaw;
        $lon = (float) $lonRaw;

        // 2. Validate physical coordinate bounds on Earth
        // Latitude: [-90 to 90], Longitude: [-180 to 180]
        if ($lat < -90.0 || $lat > 90.0 || $lon < -180.0 || $lon > 180.0) {
            return redirect()->to('/')->with('error', 'Coordinates are outside valid global boundaries (-90° to 90° lat, -180° to 180° lon).');
        }

        // 3. Request weather data from the Service
        $weatherService = new WeatherService();
        $weatherData = $weatherService->getWeather($lat, $lon);

        if ($weatherData === null) {
            return redirect()->to('/')->with('error', 'Unable to retrieve weather data from the provider at this time. Please try again in a few moments.');
        }

        // 4. Safely extract current metrics
        $current     = $weatherData['current'] ?? [];
        $weatherCode = (int) ($current['weather_code'] ?? 0);

        // 5. Format the 7-day forecast array
        $dailyRaw = $weatherData['daily'] ?? [];
        $forecast = $weatherService->formatDailyForecast($dailyRaw);

        // First day's sunrise and sunset
        $todaySunrise = $forecast[0]['sunrise'] ?? '--';
        $todaySunset  = $forecast[0]['sunset'] ?? '--';

        $data = [
            'title'   => "Weather in {$name}",
            'city'    => $name,
            'country' => $country,

            'current' => [
                'temperature'        => isset($current['temperature_2m']) ? round($current['temperature_2m'], 1) : '--',
                'feels_like'         => isset($current['apparent_temperature']) ? round($current['apparent_temperature'], 1) : '--',
                'humidity'           => $current['relative_humidity_2m'] ?? '--',
                'wind_speed'         => $current['wind_speed_10m'] ?? '--',
                'precipitation'      => $current['precipitation'] ?? 0,
                'is_day'             => $current['is_day'] ?? 1,
                'weather_description'=> $weatherService->getWeatherDescription($weatherCode),
                'weather_icon'       => $weatherService->getWeatherIcon($weatherCode),
            ],

            'forecast'     => $forecast,
            'todaySunrise' => $todaySunrise,
            'todaySunset'  => $todaySunset,
        ];

        return view('weather/dashboard', $data);
    }
}
