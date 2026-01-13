<?php

namespace App\Action\Rummy_Game\Login;

//Data
use App\Domain\Rummy_Game\Login\Data\LoginData;
use App\Domain\Rummy_Game\Login\Data\LoginDataRead;

//Service
use App\Domain\Rummy_Game\Login\Service\GoogleLoginService;
use App\Service\Auth\AuthService;
use App\Service\IP_Address\IPAddressService;

use App\Renderer\JsonRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class GoogleLoginAction
{
    private GoogleLoginService $service;
    private IPAddressService $ipAddressService;
    private AuthService $authService;

    private JsonRenderer $renderer;

    public function __construct(GoogleLoginService $service, JsonRenderer $jsonRenderer, IPAddressService $ipAddressService, AuthService $authService)
    {
        $this->service = $service;
        $this->authService = $authService;
        $this->ipAddressService = $ipAddressService;
        $this->renderer = $jsonRenderer;
    }

    public function googleLogin(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {

        
        $data = (array)$request->getParsedBody();
        unset($data['payload']); 
        
        $ip_info = $this->ipAddressService->getIpAddress($request->getServerParams());
        $ip_details = $this->ipAddressService->getIpAddressInfo($ip_info['ip_address']);

        $loginData = $this->service->googleLogin($data, $ip_info, $ip_details);

        if(!empty($loginData) && count($loginData)>0){

            if($loginData[0]['res'] == 'success'){

                $result = new LoginData();

                foreach($loginData as $userRow){

                    $users = new LoginDataRead();

                    $token_list = $this->authService->createNewToken($userRow);

                    $users->id = $userRow['id'];
                    $users->name = $userRow['name'];
                    $users->username = $userRow['username'];
                    $users->token = $token_list['token'];

                    $result->login_user[] = $users;

                }

                $ret=array("response"=>"success", "data"=>(array)$result->login_user[0]);

            }else{

                $ret=array("response"=>"failure", "err_message"=>$loginData[0]['msg']);
            }
        }else{
            $ret=array("response"=>"failure", "err_message"=>"Invalid Login");
        }               

        return $this->renderer->json($response, $ret);
    }  

    public function userLogout(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {

        
        $data = (array)$request->getParsedBody();
        $userId = $data['payload']['id'];
        unset($data['payload']); 

        //print_r($data); exit;
        
        $ip_info = $this->ipAddressService->getIpAddress($request->getServerParams());
        $ip_details = $this->ipAddressService->getIpAddressInfo($ip_info['ip_address']);

        $loginData = $this->service->userLogout($userId, $ip_info, $ip_details);

        if(!empty($loginData)){
            $ret=array("response"=>"success", "message"=>"Logout Successfully");
        }else{
            $ret=array("response"=>"failure", "err_message"=>"No Data Insert");
        }               

        return $this->renderer->json($response, $ret);
    }  

}