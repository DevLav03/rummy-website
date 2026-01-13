<?php

namespace App\Action\Rummy_Game\Login;

//Service
use App\Domain\Rummy_Game\Login\Service\ForgetService;
use App\Service\Password\PasswordService;
use App\Service\IP_Address\IPAddressService;
use App\Service\CommonService;

use App\Renderer\JsonRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class ForgetAction
{
    private ForgetService $service;
    private CommonService $commonService;
    private PasswordService $passwordService;
    private IPAddressService $ipAddressService;
    private AuthService $authService;

    private JsonRenderer $renderer;
   

    public function __construct(ForgetService $service, PasswordService $passwordService, CommonService $commonService, IPAddressService $ipAddressService, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->commonService = $commonService;
        $this->passwordService = $passwordService;
        $this->ipAddressService = $ipAddressService;
        $this->renderer = $jsonRenderer;
    }

    //Forget Email Password
    public function forgetEmailPassword(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface { 

        $data =(array)$request->getParsedBody();  
        
        $users = $this->service->getEmailUser($data);

        if(!empty($users)){

            $otp_number = $this->commonService->getOTPcode();

            $result = $this->service->forgetEmailPassword($users, $data, $otp_number);

            if(!empty($result)){
                $ret=array("response"=>"success", "message"=>'OTP Sent Successfully');
            }else{
                $ret=array("response"=>"failure", "err_message"=>'No Data Insert');
            }  

        }else{
            $ret=array("response"=>"failure", "err_message"=>'Email is dost not exits');
        }

        return $this->renderer->json($response, $ret);
    } 

    public function forgetEmailOTPVerify(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface { 

        $data =(array)$request->getParsedBody();  

        $ip_info = $this->ipAddressService->getIpAddress($request->getServerParams());

        $result = $this->service->forgetEmailOTPVerify($data);

        //print_r( $result); exit;

        if(!empty($result)){
            $ret=array("response"=>"success", "message"=>'OTP Verify Successfully');

            $this->service->updateEmailVerify($ip_info, $result[0]['id']);

        }else{
            $ret=array("response"=>"failure", "err_message"=>'Invalid OTP Code');
        }  

        return $this->renderer->json($response, $ret);
    } 

    //Forget Mobile Password
    public function forgetMobilePassword(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface { 

        $data =(array)$request->getParsedBody();  
        
        $users = $this->service->getMobileUser($data);

        if(!empty($users)){

            $otp_number = $this->commonService->getOTPcode();

            $result = $this->service->forgetMobilePassword($users, $data, $otp_number);

            if(!empty($result)){
                $ret=array("response"=>"success", "message"=>'OTP Sent Successfully');
            }else{
                $ret=array("response"=>"failure", "err_message"=>'No Data Insert');
            }  

        }else{
            $ret=array("response"=>"failure", "err_message"=>'Phone no is dost not exits');
        }

        return $this->renderer->json($response, $ret);
    } 

    public function forgetMobileOTPVerify(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface { 

        $data =(array)$request->getParsedBody();  

        $ip_info = $this->ipAddressService->getIpAddress($request->getServerParams());

        $result = $this->service->forgetMobileOTPVerify($data);       

        if(!empty($result)){
            $ret=array("response"=>"success", "message"=>'OTP Verify Successfully');

            $this->service->updateMobileVerify($ip_info, $result[0]['id']);

        }else{
            $ret=array("response"=>"failure", "err_message"=>'Invalid OTP Code');
        }  

        return $this->renderer->json($response, $ret);
    } 

    //Reset Password
    public function resetPassword(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface { 

        $data =(array)$request->getParsedBody();
       
        $result = $this->service->resetPassword($data);

        if($result == 'success'){
            if(!empty($result)){
                $ret=array("response"=>"success", "message"=>'Password updated successfully');   
            }else{
                $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
            }  
        }else if($result == 103){
            $ret=array("response"=>"failure", "err_message"=>' New password and rep new password are not same');
        }
        
        return $this->renderer->json($response, $ret);
    } 
    


   

}