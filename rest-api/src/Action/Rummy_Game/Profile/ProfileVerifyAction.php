<?php

namespace App\Action\Rummy_Game\Profile;

//Service
use App\Domain\Rummy_Game\Profile\Service\ProfileVerifyService;
use App\Service\CommonService;
use App\Service\IP_Address\IPAddressService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Slim\Factory\AppFactory;

final class ProfileVerifyAction
{
    private ProfileVerifyService $service;
    private CommonService $commonService;
    private IPAddressService $ipAddressService;
    private JsonRenderer $renderer;
   

    public function __construct(ProfileVerifyService $service, CommonService $commonService, IPAddressService $ipAddressService, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->commonService = $commonService;
        $this->ipAddressService = $ipAddressService;
        $this->renderer = $jsonRenderer;
    }

    //Mobile Verify
    public function mobileSendOTP(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
            
        $data = (array)$request->getParsedBody();
        $user_id = (int)$data['payload']['id'];
        unset($data['payload']); 

        $mobile_num = trim($data['mobile']);

        if(strlen($mobile_num) == 10){

            $mobile_count = $this->service->mobileCount($mobile_num, $user_id);

            if(empty($mobile_count)){

                $otp_number = $this->commonService->getOTPcode();

                $otp_response =$this->service->mobileSendOTP($mobile_num, $user_id, $otp_number);

                if($otp_response > 0){
                    $ret=array("response"=>"success", "message"=>"OTP Sent Successfully");
                }else{
                    $ret=array("response"=>"failure", "error_message"=>"OTP Sent Failed");
                }

            }else{
                $ret=array("response"=>"failure", "error_message"=>"Phone no is already exists");
            }
            
        }else{
            $ret=array("response"=>"failure", "error_message"=>"Invalid mobile number");
        }

        return $this->renderer->json($response, $ret);
    }

    public function mobileOTPVerify(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        
        
        $data = (array)$request->getParsedBody();
        $user_id = (int)$data['payload']['id'];
        unset($data['payload']); 

        $ip_info = $this->ipAddressService->getIpAddress($request->getServerParams());

        $verify_otp = $this->service->mobileOTPVerify($data, $user_id, $ip_info); 

        $res = $verify_otp[0]['res'];
        $msg = $verify_otp[0]['msg'];

        if(!empty($verify_otp)){

            if($res == 'success'){

                $ret=array("response"=>"success", "message"=>$msg);

            }else if($res == 'failed'){ 

                $ret=array("response"=>"failure", "err_message"=>$msg);

            }
        }else{
            $ret=array("response"=>"failure", "message"=>'Something Went Wrong!');
        }
    
        return $this->renderer->json($response, $ret);
    }

    //Email Verify
    public function emailSendOTP(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
            
        $data = (array)$request->getParsedBody();
        $user_id = (int)$data['payload']['id'];
        unset($data['payload']);     

        $email_count = $this->service->emailCount($data, $user_id);

        if(empty($email_count)){

            $otp_number = $this->commonService->getOTPcode();

            $otp_response =$this->service->emailSendOTP($data, $user_id, $otp_number);

            if($otp_response > 0){
                $ret=array("response"=>"success", "message"=>"OTP Sent Successfully");
            }else{
                $ret=array("response"=>"failure", "error_message"=>"OTP Sent Failed");
            }

        }else{
            $ret=array("response"=>"failure", "error_message"=>"Email no is already exists");
        }
        

        return $this->renderer->json($response, $ret);
    }

    public function emailOTPVerify(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        
        
        $data = (array)$request->getParsedBody();
        $user_id = (int)$data['payload']['id'];
        unset($data['payload']); 

        $ip_info = $this->ipAddressService->getIpAddress($request->getServerParams());

        $verify_otp = $this->service->emailOTPVerify($data, $user_id, $ip_info); 

        $res = $verify_otp[0]['res'];
        $msg = $verify_otp[0]['msg'];

        if(!empty($verify_otp)){

            if($res == 'success'){

                $ret=array("response"=>"success", "message"=>$msg);

            }else if($res == 'failed'){ 

                $ret=array("response"=>"failure", "err_message"=>$msg);

            }
        }else{
            $ret=array("response"=>"failure", "message"=>'Something Went Wrong!');
        }
    
        return $this->renderer->json($response, $ret);
    }
 

    
}
