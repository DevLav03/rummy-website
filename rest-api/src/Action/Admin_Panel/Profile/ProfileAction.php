<?php

namespace App\Action\Admin_Panel\Profile;

//Service
use App\Domain\Admin_Panel\Profile\Service\ProfileService;
use App\Domain\Admin_Panel\Admin\Service\AdminService;


use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class ProfileAction
{
    private ProfileService $service;
    private AdminService $adminService;

    private JsonRenderer $renderer;

    public function __construct(ProfileService $service, JsonRenderer $jsonRenderer, AdminService $adminService)
    {
        $this->service = $service;
        $this->adminService = $adminService;
       
        $this->renderer = $jsonRenderer;
    }

    //Update profile
    public function updateProfile(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
       
        $data = (array)$request->getParsedBody();
        $admin_id = $data['payload']['id'];
        unset($data['payload']);    

        $username = $data['username'];
        $email = $data['email'];

        $uname_count = $this->adminService->unameUpdateValid($username, $admin_id); 
        $email_count = $this->adminService->emailUpdateValid($email, $admin_id); 
       
        if($uname_count == 0){
            if($email_count == 0){

                $admin =$this->service->updateProfile($admin_id, $data);

                //print_r( $admin); exit;

                if($admin == 1){
                    $ret=array("response"=>"success", "message"=>'Update Successfully');
                }else if($admin == 0){
                    $ret=array("response"=>"failure", "err_message"=>'No Record Updated');
                }else{
                    $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
                }
            }
            else if($email_count == 1){
                $ret=array("response"=>"failure", "err_message"=>'Email is Already Exists');
            }       
        }
        else if($uname_count == 1){
            $ret=array("response"=>"failure", "err_message"=>'Username is Already Taken');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }

        return $this->renderer->json($response, $ret);      

    }


    //Change Password
    public function updatePassword(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface { 

        $data =(array)$request->getParsedBody();
        $admin_id = $data['payload']['id'];
        unset($data['payload']);   
        
        $result = $this->service->updatePassword($admin_id, $data);

        if($result == 'success'){
            if(!empty($result)){
                $ret=array("response"=>"success", "message"=>'Password updated successfully');   
            }else{
                $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
            }  
        }else if($result == 101){
            $ret=array("response"=>"failure", "err_message"=>'Invalid old password');
        }else if($result == 102){
            $ret=array("response"=>"failure", "err_message"=>'Old password and new password are same');
        }else if($result == 103){
            $ret=array("response"=>"failure", "err_message"=>' New password and rep new password are not same');
        }
        

        return $this->renderer->json($response, $ret);
    } 

}
