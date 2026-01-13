<?php

namespace App\Action\Master_Table\Master_Role;

//Service
use App\Domain\Master_Table\Master_Role\Service\MasterRoleService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class MasterRoleAction
{
    private MasterRoleService $service;

    private JsonRenderer $renderer;

    public function __construct(MasterRoleService $service, JsonRenderer $jsonRenderer)
    {
       
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }

    //Get Data
    public function getRoles(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
        $roles = $this->service->getRoles();

        $ret = array("response"=>"success", "data"=>$roles);
      
        return $this->renderer->json($response, $ret);

    }

    //Get Role menu data
    public function getOneRole(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface 
    {
 
        $role_id = (int)$args['role-id'];
        
        $roles = $this->service->getOneRole($role_id);

        if(!empty($roles)){
            $ret=array("response"=>"success", "data"=>$roles);
        }else{
            $ret=array("response"=>"failure", "err_message"=>"No data found");
        }

        return $this->renderer->json($response, $ret);
    } 

    //Insert Role Type
    public function insertRole(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {

        $data = (array)$request->getParsedBody();
        unset($data['payload']);   

        $roles = $this->service->insertRole($data);

        $ret=array("response"=>"success", "data"=>$roles);
      
        return $this->renderer->json($response, $ret);
    }

    //Delete Data
    public function deleteRoles(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $role_id = (int)$args['role-id'];

        //print_r($role_id); exit;

        $roles = $this->service->deleteRoles($role_id);

        if($roles == 1){
            $ret=array("response"=>"success", "message"=>'Delete Successfully');
        }else if($roles == 0){
            $ret=array("response"=>"failure", "err_message"=>"This role is already used, So we don't delete it");
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }

        return $this->renderer->json($response, $ret);

    }
       
    

   

}
