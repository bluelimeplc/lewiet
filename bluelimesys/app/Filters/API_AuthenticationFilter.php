<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class API_AuthenticationFilter implements FilterInterface
{
    private const GRANT_ACCESS = true;
    private const DENY_ACCESS = false;

    private const API_KEY_LIST = [
        'c02fb2e7eef8cc268eb4de927924ec4c_20250326' => [
            'name' => 'SMS',
            'allowed_ip' => [
                '127.0.0.1' => self::GRANT_ACCESS,
                '192.168.1.6' => self::GRANT_ACCESS,
                '192.168.1.8' => self::GRANT_ACCESS,
                '196.189.144.223' => self::GRANT_ACCESS,
                '196.188.81.138' => self::GRANT_ACCESS
            ],
        ],
    ];

    public function before(RequestInterface $request, $arguments = null)
    {
        $api_key = $_GET['api_key'] ?? '';

        $api = $this::API_KEY_LIST[$api_key] ?? null;

        if(empty($api)){
            echo 'Unauthorized API KEY';
            die();
        }
        else{
            $remote_ip = $_SERVER['REMOTE_ADDR'];
            $grant_or_deny = $api['allowed_ip'][$remote_ip] ?? false;
            $grant_or_deny = (($arguments[0]??''))=='GRANT_ANY'?true:$grant_or_deny;
            $grant_or_deny = self::GRANT_ACCESS;
            if($grant_or_deny !== self::GRANT_ACCESS){
                echo "Access is denied. Your ip address [$remote_ip] is unauthorized.";
                print_r($arguments);
                die();
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
