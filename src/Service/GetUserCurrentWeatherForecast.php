<?php
namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Flex\Response;

class GetUserCurrentWeatherForecast
{
    private $lat;
    private $lon;
    public function __construct($latitude, $longitude)
    {
        $this->lat = $latitude;
        $this->lon = $longitude;
    }
    public function getCurrentWeatherForecast(): string
    {
        $client = HttpClient::create();
        try {
            $url = 'https://api.openweathermap.org/data/2.5/weather?lat=' . $this->lat . '&lon=' . $this->lon . '&appid=' . $_ENV['OPENWEATHERMAP_API_ACCESS_KEY'];
            $response = $client->request(
                'GET',
                $url
            );
        } catch (\Exception $e) {
            return 'error: failed to make openweathermap API call';
        }

        return $response->getContent();
    }
}