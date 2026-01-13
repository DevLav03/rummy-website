<?php

namespace App\Action\Admin_Panel\Admin;

//Data
use App\Domain\Admin_Panel\Admin\Data\AdminIpData;
use App\Domain\Admin_Panel\Admin\Data\AdminIpDataRead;

//Service
use App\Domain\Admin_Panel\Admin\Service\AdminIpService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class AdminIpAction
{
    private AdminIpService $service;

    private JsonRenderer $renderer;


    public function __construct(AdminIpService $service, JsonRenderer $jsonRenderer)
    {
    
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }

    //Remove Admin IP Restrict Status
    public function adminIpStatus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {


        $admin_Id = (int)$args['admin-id'];

        $status = (int)$args['status'];

        $admin =$this->service->adminIpStatus($admin_Id, $status);

        if($admin == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($admin == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Update');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }

        return $this->renderer->json($response, $ret);

    }


    //Get Admin IP
    public function getOneAdminIp(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface { 

        $adminId = (int)$args['admin-id'];
        
        $admins = $this->service->getOneAdminIp($adminId); 

        $ret = array("response"=>"success", "data"=>$admins);
       
        return $this->renderer->json($response, $ret);
    }

    //Insert Admin IP Address
    public function insertAdminIp(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $data = (array)$request->getParsedBody();
        unset($data['payload']);       

        $ip = $this->service->checkInsertAdminIp($data);

        if($ip < 1){

            $admins = $this->service->insertAdminIp($data);

            if(!empty($admins)){
                $ret = array("response"=>"success", "data"=>['admin_ip_id'=>$admins]);
    
                $this->service->updateAdminIpStatus($data['admin_id']);
    
            }else{
                $ret=array("response"=>"failure", "message"=>'Not insert');
            }

        }else{

            $ret=array("response"=>"failure", "message"=>'IP address already exits');

        }

      

        return $this->renderer->json($response, $ret);

    }

    //Update Admin IP Address
    public function updateAdminIp(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $data = (array)$request->getParsedBody();
        unset($data['payload']);   

        $id = (int)$args['id'];

        $ip = $this->service->checkUpdateAdminIp($id, $data);

        if($ip < 1){

            $admin =$this->service->updateAdminIp($id, $data);

            if($admin > 0){

                $ret=array("response"=>"success", "message"=>'Update Successfully');
    
                $this->service->updateAdminIpStatus($id);
    
            }else{
    
                $ret=array("response"=>"failure", "err_message"=>'No Record Update');
            }

        }else{

            $ret=array("response"=>"failure", "message"=>'IP address already exits');

        }
        
      

        return $this->renderer->json($response, $ret);

    }

    //Delete Admin IP Address
    public function deleteAdminIp(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $id = (int)$args['id'];

        $admin =$this->service->deleteAdminIp($id);

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
