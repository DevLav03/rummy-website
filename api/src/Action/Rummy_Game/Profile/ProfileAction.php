<?php

namespace App\Action\Rummy_Game\Profile;

//Service
use App\Domain\Rummy_Game\Profile\Service\ProfileService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Slim\Factory\AppFactory;

final class ProfileAction
{
    private ProfileService $service;
    private JsonRenderer $renderer;
   

    public function __construct(ProfileService $service,  JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }

    //Update profile
    public function updateUserProfile(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
    
        $data = (array)$request->getParsedBody();
        $user_id = $data['payload']['id'];
        unset($data['payload']);    

        $user =$this->service->updateUserProfile($user_id, $data);

            if($user == 1){
                $ret=array("response"=>"success", "message"=>'Update Successfully');
            }else if($user == 0){
                $ret=array("response"=>"failure", "err_message"=>'No Record Updated');
            }else{
                $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
            }
            

        return $this->renderer->json($response, $ret);      

    }


    //Change Password
    public function changePassword(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface { 

        $data =(array)$request->getParsedBody();
        $user_id = $data['payload']['id'];
        unset($data['payload']);   
        
        $result = $this->service->changePassword($user_id, $data);

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
    
  
    //Insert
    public function uploadProfileImage(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $uploadedFiles = $request->getUploadedFiles();
        $user_id = $data['payload']['id'];
        unset($data['payload']); 
        
        if (array_key_exists('profile_image', $uploadedFiles)) {
            $file = $uploadedFiles['profile_image']; 
            $imageInsert = $this->service->uploadProfileImage($data, $user_id, $file);
        }else{
            $imageInsert = $this->service->uploadProfileImage($data,$user_id, null);
        } 
       
        if($imageInsert == 0){
            $ret=array("response"=>"success", "message"=>'Insert Successfully');
        }else if($imageInsert == 1){
            $ret=array("response"=>"failure", "err_message"=>'Upload File Error');
        }else if($imageInsert == 2){
            $ret=array("response"=>"failure", "err_message"=>'Upload File Size Limit 2mb');
        }else if($imageInsert == 3){
            $ret=array("response"=>"failure", "err_message"=>'Updalod File Type png,jpg,jpeg');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No Data Insert');
        }
    
        return $this->renderer->json($response, $ret);
    }

}
