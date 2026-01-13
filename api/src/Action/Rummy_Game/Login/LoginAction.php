<?php

namespace App\Action\Rummy_Game\Login;

//Data
use App\Domain\Rummy_Game\Login\Data\LoginData;
use App\Domain\Rummy_Game\Login\Data\LoginDataRead;

//Service
use App\Domain\Rummy_Game\Login\Service\LoginService;
use App\Service\Auth\AuthService;
use App\Service\Password\PasswordService;
use App\Service\IP_Address\IPAddressService;
use App\Service\CommonService;

use App\Renderer\JsonRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\Service\SMS\SmsService;
final class LoginAction
{
    private LoginService $service;
    private CommonService $commonService;
    private PasswordService $passwordService;
    private IPAddressService $ipAddressService;
    private AuthService $authService;

    private JsonRenderer $renderer;
   
private SmsService $smsService;

    public function __construct(LoginService $service, SmsService $smsService, PasswordService $passwordService, CommonService $commonService, IPAddressService $ipAddressService,  AuthService $authService, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->commonService = $commonService;
        $this->passwordService = $passwordService;
        $this->authService = $authService;
        $this->ipAddressService = $ipAddressService;
        $this->renderer = $jsonRenderer;
        $this->smsService = $smsService;
    }

    //Login With Password
    public function loginPassword(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
         
        $data = (array)$request->getParsedBody();

        //print_r($data); exit;
      
        $username = $data['mobile_or_mail'];
        $password = $this->passwordService->passwordEncrytion($data['password']);
        
        $ip_info = $this->ipAddressService->getIpAddress($request->getServerParams());
        //print_r($ip_info); exit;
        $ip_details = $this->ipAddressService->getIpAddressInfo($ip_info['ip_address']);
        //print_r($ip_details); exit;

        $loginData = $this->service->loginPassword($username, $password, $ip_info, $ip_details);

        //print_r($loginData); exit;

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

    //OTP Login
    public function otpLogin(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        
       
        $data = (array)$request->getParsedBody();

        //$ip_info = $this->ipAddressService->getIpAddress($request->getServerParams());
        //$ip_details = $this->ipAddressService->getIpAddressInfo($ip_info['ip_address']);
        //print_r($ip_details); exit;

        //if($ip_details['country'] == 'India'){
            //$state = $this->service->stateCount($ip_details['regionName']);
            //if($state == 1){

                $loginData = $this->service->otpLogin($data);

                //print_r($loginData);exit;

                if($loginData == 1){

                    $otp_number = $this->commonService->getOTPcode();

                    $insert_otp = $this->service->insertOTPcode($data['phone_no'], $otp_number);

                    //print_r($insert_otp); exit;

                    if($insert_otp > 0){
        
                        $res=$this->smsService->SendOTPcode($data['phone_no'],$otp_number); //OTP Service
                        //var_dump($res);exit; 

                        $ret=array("response"=>"success","otp_no"=>$otp_number, "message"=>"OTP Sent Successfully");
        
                       
        
                    }else{
                        $ret=array("response"=>"failure", "err_message"=>"OTP Insert Data Failed");
                    } 
        
                }else{
                    $ret=array("response"=>"failure", "err_message"=>'Phone number does not exits');
                }
            //}
            //else{
                //$ret=array("response"=>"failure", "err_message"=>"Unauthorized ".$ip_details['regionName']." State");
            //}
        //}else{
            //$ret=array("response"=>"failure", "err_message"=>"Unauthorized ".$ip_details['country']." Country");
        //}

        return $this->renderer->json($response, $ret);
    }

    public function otpLoginVerify(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        
       
        $data = (array)$request->getParsedBody();

        $ip_info = $this->ipAddressService->getIpAddress($request->getServerParams());
        $ip_details = $this->ipAddressService->getIpAddressInfo($ip_info['ip_address']);

        $loginData = $this->service->otpLoginVerify($data, $ip_info, $ip_details);


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


    //Last Login Details
    public function LastLogin(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
       
        $logindetails = $this->service->LastLogin();

        $ret=array("response"=>"success", "data"=>$logindetails);
        
        return $this->renderer->json($response, $ret);

    }
}