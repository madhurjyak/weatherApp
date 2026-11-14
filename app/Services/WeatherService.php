<?php

namespace App\Services;

use CodeIgniter\HTTP\CURLRequest;

class WeatherService
{
    /**
     * The HTTP client used to send requests to external APIs.
     */
    private CURLRequest $client;

    /**
     * Base URL for the Geocoding API (city name → coordinates).
     */
    private string $geocodingUrl = 'https://geocoding-api.open-meteo.com/v1/search';

    /**
     * Base URL for the Forecast API (coordinates → weather data).
     */
    private string $forecastUrl = 'https://api.open-meteo.com/v1/forecast';

    /**
     * Weather code mapping for WMO codes.
     */
    private array $weatherCodes = [
        0  => ['description' => 'Clear sky',                    'icon' => '☀️'],
        1  => ['description' => 'Mainly clear',                 'icon' => '🌤️'],
        2  => ['description' => 'Partly cloudy',                'icon' => '⛅'],
        3  => ['description' => 'Overcast',                     'icon' => '☁️'],
        45 => ['description' => 'Fog',                          'icon' => '🌫️'],
        48 => ['description' => 'Depositing rime fog',          'icon' => '🌫️'],
        51 => ['description' => 'Light drizzle',                'icon' => '🌦️'],
        53 => ['description' => 'Moderate drizzle',             'icon' => '🌦️'],
        55 => ['description' => 'Dense drizzle',                'icon' => '🌦️'],
        61 => ['description' => 'Slight rain',                  'icon' => '🌧️'],
        63 => ['description' => 'Moderate rain',                'icon' => '🌧️'],
        65 => ['description' => 'Heavy rain',                   'icon' => '🌧️'],
        66 => ['description' => 'Light freezing rain',          'icon' => '🌧️'],
        67 => ['description' => 'Heavy freezing rain',          'icon' => '🌧️'],
        71 => ['description' => 'Slight snowfall',              'icon' => '🌨️'],
        73 => ['description' => 'Moderate snowfall',            'icon' => '🌨️'],
        75 => ['description' => 'Heavy snowfall',               'icon' => '❄️'],
        77 => ['description' => 'Snow grains',                  'icon' => '❄️'],
        80 => ['description' => 'Slight rain showers',          'icon' => '🌦️'],
        81 => ['description' => 'Moderate rain showers',         'icon' => '🌧️'],
        82 => ['description' => 'Violent rain showers',          'icon' => '⛈️'],
        85 => ['description' => 'Slight snow showers',          'icon' => '🌨️'],
        86 => ['description' => 'Heavy snow showers',           'icon' => '🌨️'],
        95 => ['description' => 'Thunderstorm',                 'icon' => '⛈️'],
        96 => ['description' => 'Thunderstorm with slight hail', 'icon' => '⛈️'],
        99 => ['description' => 'Thunderstorm with heavy hail',  'icon' => '⛈️'],
    ];

    public function __construct()
    {
        // 8-second timeout: Prevents the server from hanging if Open-Meteo is slow
        $this->client = service('curlrequest', [
            'timeout'         => 8,
            'connect_timeout' => 5,
            'http_errors'     => false, // Don't throw exceptions on 4xx/5xx responses; handle status codes manually
        ]);
    }

    /**
     * Search for cities matching the given name with exception handling.
     *
     * @param string $cityName The city name to search for
     * @return array List of matching cities or empty array on failure
     */
    public function searchCity(string $cityName): array
    {
        try {
            $response = $this->client->get($this->geocodingUrl, [
                'query' => [
                    'name'     => $cityName,
                    'count'    => 5,
                    'language' => 'en',
                    'format'   => 'json',
                ],
            ]);

            // Check HTTP status code
            if ($response->getStatusCode() !== 200) {
                log_message('error', "Geocoding API responded with HTTP status {$response->getStatusCode()} for city: '{$cityName}'");
                return [];
            }

            // Decode JSON
            $body = $response->getBody();
            $data = json_decode($body, true);

            // Validate JSON decoding
            if (json_last_error() !== JSON_ERROR_NONE) {
                log_message('error', "Failed to decode Geocoding JSON response for '{$cityName}': " . json_last_error_msg());
                return [];
            }

            return $data['results'] ?? [];

        } catch (\Throwable $e) {
            // Catch timeouts, network outages, DNS failures
            log_message('error', "Geocoding API request exception for city '{$cityName}': " . $e->getMessage());
            return [];
        }
    }

    /**
     * Fetch weather data by coordinates with exception handling and payload validation.
     *
     * @param float $latitude
     * @param float $longitude
     * @return array|null Weather data array or null on failure
     */
    public function getWeather(float $latitude, float $longitude): ?array
    {
        try {
            $response = $this->client->get($this->forecastUrl, [
                'query' => [
                    'latitude'      => $latitude,
                    'longitude'     => $longitude,
                    'current'       => 'temperature_2m,relative_humidity_2m,apparent_temperature,is_day,precipitation,weather_code,wind_speed_10m',
                    'daily'         => 'weather_code,temperature_2m_max,temperature_2m_min,sunrise,sunset,uv_index_max',
                    'timezone'      => 'auto',
                    'forecast_days' => 7,
                ],
            ]);

            if ($response->getStatusCode() !== 200) {
                log_message('error', "Forecast API responded with HTTP status {$response->getStatusCode()} for coordinates: ({$latitude}, {$longitude})");
                return null;
            }

            $body = $response->getBody();
            $data = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                log_message('error', "Failed to decode Forecast JSON response: " . json_last_error_msg());
                return null;
            }

            // Ensure expected keys exist
            if (! isset($data['current']) || ! isset($data['daily'])) {
                log_message('error', 'Forecast API response is missing expected "current" or "daily" data structures.');
                return null;
            }

            return $data;

        } catch (\Throwable $e) {
            log_message('error', "Forecast API request exception: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Transform raw parallel arrays from the API's 'daily' object safely.
     */
    public function formatDailyForecast(array $daily): array
    {
        $formatted = [];
        $times = $daily['time'] ?? [];

        if (! is_array($times)) {
            return [];
        }

        foreach ($times as $index => $timeString) {
            try {
                $date = new \DateTime($timeString);
                $dayLabel = $index === 0 ? 'Today' : $date->format('D');
                $dateFormatted = $date->format('M j');
            } catch (\Throwable $e) {
                $dayLabel = 'Day ' . ($index + 1);
                $dateFormatted = '--';
            }

            $weatherCode = (int) ($daily['weather_code'][$index] ?? 0);

            // Safely parse sunrise & sunset timestamps
            $sunriseFormatted = '--';
            if (! empty($daily['sunrise'][$index])) {
                try {
                    $sunriseFormatted = (new \DateTime($daily['sunrise'][$index]))->format('h:i A');
                } catch (\Throwable $e) {
                    $sunriseFormatted = '--';
                }
            }

            $sunsetFormatted = '--';
            if (! empty($daily['sunset'][$index])) {
                try {
                    $sunsetFormatted = (new \DateTime($daily['sunset'][$index]))->format('h:i A');
                } catch (\Throwable $e) {
                    $sunsetFormatted = '--';
                }
            }

            $formatted[] = [
                'day_name'    => $dayLabel,
                'date'        => $dateFormatted,
                'condition'   => $this->getWeatherDescription($weatherCode),
                'icon'        => $this->getWeatherIcon($weatherCode),
                'temp_max'    => isset($daily['temperature_2m_max'][$index]) ? round($daily['temperature_2m_max'][$index]) : '--',
                'temp_min'    => isset($daily['temperature_2m_min'][$index]) ? round($daily['temperature_2m_min'][$index]) : '--',
                'uv_index'    => $daily['uv_index_max'][$index] ?? '--',
                'sunrise'     => $sunriseFormatted,
                'sunset'      => $sunsetFormatted,
            ];
        }

        return $formatted;
    }

    /**
     * Convert a weather code number into a readable description.
     */
    public function getWeatherDescription(int $code): string
    {
        return $this->weatherCodes[$code]['description'] ?? 'Unknown Weather';
    }

    /**
     * Get the emoji icon for a weather code.
     */
    public function getWeatherIcon(int $code): string
    {
        return $this->weatherCodes[$code]['icon'] ?? '🌡️';
    }
}
