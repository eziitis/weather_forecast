<?php

namespace App\Controller;

use App\Entity\WeatherForecast;
use App\Service\GetUserCurrentWeatherForecast;
use App\Service\GetUserIPAddressService;
use App\Service\GetUserLocationService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;

class HomeController extends AbstractController
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $cache = new FilesystemAdapter();
        $json_data = $cache->get('weather_forecast_data', function (ItemInterface $item) {
            $item->expiresAfter(3600);

            return $this->getCurrentWeatherJsonDataString();
        });

        if (!str_contains($json_data, 'error')) {
            $decoded = json_decode($json_data);
            $error = '';
        } else {
            $error = $json_data;
        }

        return $this->render('index.html.twig', [
            'controller_name' => 'HomeController',
            'json_data' => $error === '' ? json_encode($decoded, constant('JSON_PRETTY_PRINT')) : '',
            'error' => $error
        ]);
    }

    public function getCurrentWeatherJsonDataString(): string
    {
        $get_ip_service = new GetUserIPAddressService();
        $user_ip_address = $get_ip_service->getUserIPAddress();
        //$user_ip_address = '134.201.250.155'; //for testing
        $weather_forecast = $this->doctrine->getRepository(WeatherForecast::class)->findBy(['ip_address' => $user_ip_address]);

        if (count($weather_forecast) > 0) {
            return $weather_forecast[0]->getWeatherForecast(); //only one record with such IP address (unique constraint)
        } else {
            if (!str_contains($user_ip_address, 'error')) {
                $current_weather_forecast = $this->getWeatherData($user_ip_address);
                $entityManager = $this->doctrine->getManager();

                $weather_forecast = new WeatherForecast();
                $weather_forecast->setIpAddress($user_ip_address);
                $weather_forecast->setWeatherForecast($current_weather_forecast);
                $entityManager->persist($weather_forecast);
                $entityManager->flush();

                return $current_weather_forecast;
            } else {
                return $user_ip_address;
            }
        }
    }

    #[Route('/refresh_weather_data', name: 'refresh_weather_data')]
    public function refreshWeatherData(): Response
    {
        $cache = new FilesystemAdapter();
        $cache->deleteItem('weather_data');

        $get_ip_service = new GetUserIPAddressService();
        $user_ip_address = $get_ip_service->getUserIPAddress();
        //$user_ip_address = '134.201.250.155'; //for testing
        $weather_forecast = $this->doctrine->getRepository(WeatherForecast::class)->findBy(['ip_address' => $user_ip_address]);
        $entityManager = $this->doctrine->getManager();

        if (count($weather_forecast) > 0) {
            $forecast_data = $this->getWeatherData($user_ip_address);

            if (!str_contains($forecast_data, 'error')) {
                $decoded = json_decode($forecast_data);
                $error = '';

                $item = $weather_forecast[0];
                $item->setWeatherForecast($forecast_data);
                $entityManager->flush();
            } else {
                $error = $forecast_data;
            }

            return $this->render('index.html.twig', [
                'controller_name' => 'HomeController',
                'json_data' => $error === '' ? json_encode($decoded, constant('JSON_PRETTY_PRINT')) : '',
                'error' => $error
            ]);
        } else {
            return $this->render('index.html.twig', [
                'controller_name' => 'HomeController',
                'json_data' => '',
                'error' => 'Error: did\'n find DB record'
            ]);
        }
    }

    public function getWeatherData(string $user_ip_address): string
    {
        $get_user_location_service = new GetUserLocationService($user_ip_address);
        $user_location_data = $get_user_location_service->getUserLocation();
        $cache = new FilesystemAdapter();
        $weather_data = $cache->get('weather_data', function (ItemInterface $item) use ($user_location_data) {
            $item->expiresAfter(3600);

            if (array_key_exists('ip', $user_location_data) && !str_contains($user_location_data['ip'], 'error')) {
                $get_user_weather_forecast_service = new GetUserCurrentWeatherForecast($user_location_data['latitude'], $user_location_data['longitude']);

                return $get_user_weather_forecast_service->getCurrentWeatherForecast();
            } else {
                if (array_key_exists('ip', $user_location_data)) {
                    return $user_location_data['ip'];
                } else {
                    return 'error: unknown';
                }
            }
        });

        return $weather_data;
    }
}
