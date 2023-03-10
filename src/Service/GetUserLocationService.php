<?php
namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;

class GetUserLocationService
{
    private string $ip_address;

    public function __construct(string $ip_address)
    {
        $this->ip_address = $ip_address;
    }
    public function getUserLocation(): array
    {
        try {
            $client = HttpClient::create();
            $url = 'http://api.ipstack.com/' . $this->ip_address . '?access_key=' . $_ENV['IPSTACK_API_ACCESS_KEY'];
            $response = $client->request(
                'GET',
                $url
            );
        } catch (\Exception $e) {
            return ['ip' =>'error: failed to make ipstack API call'];
        }


        return $response->toArray();
    }
}