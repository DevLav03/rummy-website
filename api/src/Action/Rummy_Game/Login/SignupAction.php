<?php

namespace App\Action\Rummy_Game\Login;

//Data
use App\Domain\Rummy_Game\Login\Data\LoginData;
use App\Domain\Rummy_Game\Login\Data\LoginDataRead;

//Service
use App\Domain\Rummy_Game\Login\Service\SignupService;
use App\Service\Auth\AuthService;
use App\Service\SMS\SmsService;
use App\Service\IP_Address\IPAddressService;
use App\Service\CommonService;

use App\Renderer\JsonRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class SignupAction
{
    private SignupService $service;
    private CommonService $commonService;
    private IpAddressService $ipAddressService;

    private JsonRenderer $renderer;
    private AuthService $authService;

    private $phone_no;
    private $ref_code;

    public function __construct(SignupService $service, JsonRenderer $jsonRenderer, CommonService $commonService,  AuthService $authService, IpAddressService $ipAddressService)
    {
        $this->service = $service;
        $this->commonService = $commonService;
        $this->authService = $authService;
        $this->ipAddressService = $ipAddressService;

        $this->renderer = $jsonRenderer;
      
    }

    public function usersRegsiter(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {

        $data = (array)$request->getParsedBody();

        // $ip_info = $this->ipAddressService->getIpAddress($request->getServerParams());
        // $ip_details = $this->ipAddressService->getIpAddressInfo($ip_info['ip_address']);
        // //print_r($ip_details); exit;

        // if($ip_details['country'] == 'India'){
        //     $state= $this->service->stateCount($ip_details['regionName']);
        //     if($state == 1){

                $loginData = $this->service->usersRegsiter($data);

                $phone_no = $data['phone_no'];
                $ref_code = $data['ref_code'];

                if($loginData == 0){

                    $otp_number = $this->commonService->getOTPcode();

                    $insert_otp = $this->service->insertOTPcode($phone_no, $otp_number);

                    if($insert_otp > 0){

                        $ret=array("response"=>"success", "message"=>"OTP Sent Successfully");

                        //$this->SmsService->SendOTPcode($otp_number); //OTP Service

                    }else{
                        $ret=array("response"=>"failure", "err_message"=>"OTP Insert Data Failed");
                    }      

                }else{
                    $ret=array("response"=>"failure", "err_message"=>"Phone No is Already Exits");
                }
        //     }
        //     else{
        //         $ret=array("response"=>"failure", "err_message"=>"Unauthorized ".$ip_details['regionName']." State");
        //     }
        // }else{
        //     $ret=array("response"=>"failure", "err_message"=>"Unauthorized ".$ip_details['country']." Country");
        // }


        return $this->renderer->json($response, $ret);
    }


    
    public function usersRegsiterVerify(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {

        $data = (array)$request->getParsedBody();

        $ip_info = $this->ipAddressService->getIpAddress($request->getServerParams());

        $ip_details = $this->ipAddressService->getIpAddressInfo($ip_info['ip_address']);

        $loginData = $this->service->usersRegsiterVerify($data, $ip_info, $ip_details); 

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
            $ret=array("response"=>"failure", "err_message"=>"Something Went Wrong");
        }

        return $this->renderer->json($response, $ret);
    }

}

