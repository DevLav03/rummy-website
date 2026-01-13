<?php

namespace App\Action\Admin_Panel\Admin;

//Service
use App\Domain\Admin_Panel\Admin\Service\AdminService;
use App\Service\Password\PasswordService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class AdminAction
{
    private AdminService $service;
    private PasswordService $passwordService;
    private JsonRenderer $renderer;

    public function __construct(AdminService $service, JsonRenderer $jsonRenderer, PasswordService $passwordService)
    {
        $this->service = $service;
        $this->passwordService = $passwordService;
        $this->renderer = $jsonRenderer;
    }

    //Get All Data
    public function getAdmins(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
       
        $admins = $this->service->getAdmins();

        $ret=array("response"=>"success", "data"=>$admins);
        
        return $this->renderer->json($response, $ret);

    }

    //Get One Data
    public function getOneAdmin(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface { 

        $adminId = (int)$args['admin-id'];
        
        $admins = $this->service->getOneAdmin($adminId); 

        if(!empty($admins)){

            $ret=array("response"=>"success", "data"=>$admins);

        }else{

            $ret=array("response"=>"failure", "err_message"=>'No data found');

        }

        return $this->renderer->json($response, $ret);
    } 

    //Insert Data
    public function insertAdmin(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

        $data = (array)$request->getParsedBody();
        unset($data['payload']);   

        $username = $data['username'];
        $password = $this->passwordService->passwordEncrytion($data['password']);
        $email = $data['email'];

        $uname_count = $this->service->unameInsertValid($username); 
        $email_count = $this->service->emailInsertvalid($email); 

        if($uname_count == 0){

            if($email_count == 0){

                $admins = $this->service->insertAdmin($data, $password);

                if(!empty($admins)){
                    $ret=array("response"=>"success", "data"=>["admin-id"=>$admins]);
                }else{
                    $ret=array("response"=>"failure", "err_message"=>'No Data Insert');
                }
            }
            else if($email_count > 0){
                $ret=array("response"=>"failure", "err_message"=>'Email is already exists');
            }           
        }
        else if($uname_count > 0){
            $ret=array("response"=>"failure", "err_message"=>'Username is already taken');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }
        
        
        return $this->renderer->json($response, $ret);
    }

    //Update Data
    public function updateAdmin(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
       
        $data=(array)$request->getParsedBody();
        unset($data['payload']);

        $admin_id = (int)$args['admin-id'];
        $text_password = $data['password'];

        if($text_password != null){
            $password = $this->passwordService->passwordEncrytion($data['password']);
        }else{
            $password = 'null';
        }

        $username = $data['username'];
        $email = $data['email'];

        $uname_count = $this->service->unameUpdateValid($username, $admin_id); 
        $email_count = $this->service->emailUpdateValid($email, $admin_id); 
 
        if($uname_count == 0){
            if($email_count == 0){

                $data['password'] = $password;

                $admin = $this->service->updateAdmin($admin_id, $data);

                if($admin == 1){
                    $ret=array("response"=>"success", "message"=>'Update Successfully');
                }else if($admin == 0){
                    $ret=array("response"=>"failure", "err_message"=>'No Record Updated');
                }else{
                    $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
                }
            }
            else if($email_count > 0){
                $ret=array("response"=>"failure", "err_message"=>'Email is Already Exists.');
            }           
        }
        else if($uname_count > 0){
            $ret=array("response"=>"failure", "err_message"=>'Username is Already Taken.');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }
    
        return $this->renderer->json($response, $ret);    
      
    }

    //block and unblock
    public function blockAdmin(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $admin_id = (int)$args['admin-id'];
        $active = (int)$args['active'];

        $admin = $this->service->blockAdmin($admin_id, $active);

        if($admin == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($admin == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Updated');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }
        
        return $this->renderer->json($response, $ret);

    }

    //Delete Data
    public function deleteAdmin(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $admin_Id = (int)$args['admin-id'];
        $admin = $this->service->deleteAdmin($admin_Id);

        if($admin == 1){
            $ret=array("response"=>"success", "message"=>'Delete Successfully');
        }else if($admin == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Delete');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }

        return $this->renderer->json($response, $ret);

    }
       

}
