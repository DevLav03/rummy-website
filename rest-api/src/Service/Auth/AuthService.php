<?php

namespace App\Service\Auth;

use Psr\Log\LoggerInterface; 
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use PDO;

final class AuthService
{

    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }
    
    public function createNewToken($payLoad){
            $token_list=array();
            $secretKey='abc123';
            $token_payload = array(
                'id'   	=> $payLoad['id'],
                'name'   	=> $payLoad['name'],
                'username' 		=>  $payLoad['username'],
                'iat'   		=> time(),
                'exp'   		=> time() + (60*60*24*1),
                //'exp'   		=> time() + 10, 
            );
            $token = JWT::encode($token_payload, $secretKey, 'HS256');
            $token_list['token'] = $token;
            $refresh_token_payload = array(
                'id'   	=> $payLoad['id'],
                'name'   	=> $payLoad['name'],
                'username' 		=>  $payLoad['username'],
                'iat'   		=> time(),
                'exp'   		=> time() + (60*60*24*1)+(3600),
                //'exp'   		=> time() + 20, 
            );
            $rToken = JWT::encode($refresh_token_payload, $secretKey, 'HS256');
            $token_list['refresh_token'] = $rToken;
            
            return $token_list;
    }

    public function createUserToken($payLoad){
        $token_list=array();
        $secretKey='abc123';
        $token_payload = array(
            'id'   	=> $payLoad['id'],
            'name'   	=> $payLoad['name'],
            'username' 		=>  $payLoad['username'],
            'iat'   		=> time(),
            'exp'   		=> time() + (60*60*24*1),
           
        );
        $token = JWT::encode($token_payload, $secretKey, 'HS256');
        $token_list['token'] = $token;
        
        return $token_list;
    }

}

   
