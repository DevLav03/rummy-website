<?php

namespace App\Domain\Service;

/**
 * Service
 */
final class CommonService {


    public function __construct()
    {
        
    }

    public function checkValidEmail(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface { 

        
        
        return $this->renderer->json($response, $ret);
    }    

    public function getOTPcode():int
    {

        $otp_6_digit = rand(100000,999999);

        return $otp_6_digit;

    }

    public function getRefCode():string
    {

        $str_result = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'; 
        $ref_code = substr(str_shuffle($str_result), 0, 8); 
        return $ref_code;

    }

   
}