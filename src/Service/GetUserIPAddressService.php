<?php
namespace App\Service;

class GetUserIPAddressService
{
    public function getUserIPAddress(): string
    {
        //whether ip is from the share internet
        if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip_address = $_SERVER['HTTP_CLIENT_IP'];
        }
        //whether ip is from the proxy
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        //whether ip is from the remote address
        else{
            $ip_address = $_SERVER['REMOTE_ADDR'];
        }

        return is_string($ip_address) ? $ip_address : 'error: IP address not found';
    }
}