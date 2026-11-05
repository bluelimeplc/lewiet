<?php

namespace App\Filters;

use BluelimeSystem;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class API2_AuthenticationFilter implements FilterInterface
{
    private const GRANT_ACCESS = true;
    private const DENY_ACCESS = false;

    private const API_LIST = [
        'FINANCE' => [
            'V1' => [
                'c02fb2e7eef8cc268eb4de927924ec4c_20250808' => [
                    'description' => 'For credit payment request',
                    'expiry_date' => NULL,
                    'ip_start_with_access' => [
                        '196.189' => self::GRANT_ACCESS,
                        //'253.145' => self::DENY_ACCESS,
                    ],
                    'ip_match_access' => [
                        '127.0.0.1' => self::GRANT_ACCESS,
                        '192.168.1.6' => self::GRANT_ACCESS,
                        '0.0.0.0' => self::DENY_ACCESS,
                    ],
                ]
            ],
        ],
    ];

    private static $error_response = [];

    private function check_ip_start_with_access($ip_access_list, $ip_address){
        $grant_or_deny = NULL;
        foreach($ip_access_list as $value){
            if(str_starts_with($ip_address, $value['ip_address'])){
                $grant_or_deny = $value['access'] == 'grant' ? self::GRANT_ACCESS : self::DENY_ACCESS;
                break;
            }
        }
        return $grant_or_deny;
    }

    private function check_exact_ip_access($ip_access_list, $ip_address){
        $grant_or_deny = NULL;
        foreach($ip_access_list as $value){
            if($ip_address == $value['ip_address']){
                $grant_or_deny = $value['access'] == 'grant' ? self::GRANT_ACCESS : self::DENY_ACCESS;
                break;
            }
        }
        return $grant_or_deny;
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        $api_key = $_GET['api_key'] ?? '';
        $segments = $request->getUri()->getSegments();
        $api_group = $segments[0] ?? '';
        $api_group = strtoupper($api_group);
        $api_name = $segments[1] ?? '';
        $api_name = strtoupper($api_name);
        $api_version = $segments[2] ?? '';
        $api_version = strtoupper($api_version);

        $apiModel = model('System/APIModel');

        $api = $apiModel->where('api_group', $api_group)->where('name',$api_name)->where('api_version', $api_version)->findAll();
        $api = $api[0] ?? null;
        //$api = self::API_LIST[$api_name] ?? null;

        //BluelimeSystem::print_associated_array($api);

        $version = $api['api_version'] ?? null;
        $api_data = $api['data'] ?? [];

        $key = null;
        foreach($api_data as $value){
            if($value['api_key'] === $api_key){
                $key = $value;
                break;
            }
        }

        if($api === null){
            self::$error_response = [
                'message' => 'Unkown API. \''.$api_name.'\' is not found.',
                'status' => 'error',
                'data' => []
            ];
            return false; // Stop further processing if key is expired
        }
        elseif($api['active'] == false){
            self::$error_response = [
                'message' => 'API is disabled. \'',
                'status' => 'error',
                'data' => []
            ];
            return false; // Stop further processing if key is expired
        }
        
        if($version === null){
            self::$error_response = [
                'message' => 'Unkown API version. \''.$api_version.'\' is not found.',
                'status' => 'error',
                'data' => []
            ];
            return false; // Stop further processing if key is expired
        }
        
        if($key === null){
            self::$error_response = [
                'message' => 'Unkown API key. \''.$api_key.'\' is not found.',
                'status' => 'error',
                'data' => []
            ];
            return false; // Stop further processing if key is expired
        }

        if(!empty($key['expiry_date']) && strtotime($key['expiry_date']) < time()){
            self::$error_response = [
                'message' => 'API key is expired.',
                'status' => 'error',
                'data' => []
            ];
            return false; // Stop further processing if key is expired
        }

        if(!$key['active']){
            self::$error_response = [
                'message' => 'API key is not enabled.',
                'status' => 'error',
                'data' => []
            ];
            return false; // Stop further processing if key is expired
        }
            
        $remote_ip = $_SERVER['REMOTE_ADDR'];
        $grant_or_deny = NULL;

        $grant_or_deny = $this->check_ip_start_with_access($key['ip_start_with_access'], $remote_ip);
        $grant_or_deny_2 = $this->check_exact_ip_access($key['ip_exact_match_access'], $remote_ip);
        
        if($grant_or_deny_2 !== NULL)
            $grant_or_deny = $grant_or_deny_2;

        //If allow for any
        $default_ip_rule = self::DENY_ACCESS;
        foreach($key['ip_exact_match_access'] as $value){
            if($value['ip_address'] === '0.0.0.0'){
                $default_ip_rule = ($value['access']=='grant') ? self::GRANT_ACCESS : self::DENY_ACCESS;
                break;
            }
        }
        if($grant_or_deny === NULL)
            $grant_or_deny = $default_ip_rule;

        $grant_or_deny = (($arguments[0]??''))=='GRANT_ANY'?self::GRANT_ACCESS:$grant_or_deny;
        
        if($grant_or_deny !== self::GRANT_ACCESS){
            self::$error_response = [
                'message' => "Access is denied. Your ip address [$remote_ip] is unauthorized.",
                'status' => 'error',
                'data' => []
            ];
        }
    }    

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        if(!empty(self::$error_response) && (($arguments[0]??'')!=='GRANT_ANY')){
            $response->setJSON(self::$error_response);
            $response->setStatusCode(403); // Forbidden
            return $response;
        }
    }
}
