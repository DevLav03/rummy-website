<?php

namespace App\Service\IP_Address;

use App\Factory\LoggerFactory; 
use Psr\Log\LoggerInterface; 

/**
 * Service
 */
final class IPAddressService {


    public function __construct(LoggerFactory $loggerFactory)
    {
        $this->logger = $loggerFactory->addFileHandler('rummy.log')->createLogger();
    }

    public function getIpAddress($ip_info): array{
     
        $server_values_array = $ip_info;
        $device_type=!empty($server_values_array['HTTP_USER_AGENT'])?$server_values_array['HTTP_USER_AGENT']:'';
        if (!empty($server_values_array['HTTP_CLIENT_IP'])) {
            $ip_address = $server_values_array['HTTP_CLIENT_IP'];
        } elseif (!empty($server_values_array['HTTP_X_FORWARDED_FOR'])) {
            $ip_address = $server_values_array['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip_address = $server_values_array['REMOTE_ADDR'];
        }

        $valid = preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/', $ip_address);
        if(!$valid){
            $ip_address = '27.5.216.189'; //Only test
        }

        return [
            'ip_address' => $ip_address,
            'device_type' => $device_type
        ];
       
    }

    public function getIpAddressInfo($ip_address){
        try{
            $url = 'http://ip-api.com/json/'.$ip_address;
         
            $crl = curl_init();
            curl_setopt($crl, CURLOPT_URL, $url);
            curl_setopt($crl, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
             
            $response = curl_exec($crl);

            if(!$response){
                //$errorMsg = die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
                $loggerFactory->addFileHandler('get_ip_address_info.log')->createLogger()->info(sprintf('Get IP Address Info: %s', $errorMsg)); 
                return array();
            }

            curl_close($crl);

            $responseArray = json_decode($response, true);

            return $responseArray;

        }catch(Exception $ex){
            $errorMsg = $ex->getMessage();
            $loggerFactory->addFileHandler('get_ip_address_info.log')->createLogger()->info(sprintf('Get IP Address Info: %s', $errorMsg));
            return array();
        }

    }
}