<?php

namespace App\Controller;

use App\Service\GetUserIPAddressService;
use App\Service\GetUserLocationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $get_ip_service = new GetUserIPAddressService();
        $user_ip_address = $get_ip_service->getUserIPAddress();

        //for testing
        $user_ip_address = '134.201.250.155';
        $get_user_location_service = new GetUserLocationService($user_ip_address);
        $user_location_data = $get_user_location_service->getUserLocation();



        dd($get_user_location_service->getUserLocation());

        return $this->render('index.html.twig', [
            'controller_name' => 'HomeController',
            'json_data' => 'test',
        ]);

//        return $this->json([
//            'controller_name' => 'HomeController'
//        ]);
    }
}
