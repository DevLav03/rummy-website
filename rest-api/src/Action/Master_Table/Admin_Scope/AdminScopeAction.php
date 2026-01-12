<?php

namespace App\Action\Master_Table\Admin_Scope;

//Service
use App\Domain\Master_Table\Admin_Scope\Service\AdminScopeService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class AdminScopeAction
{
    private AdminScopeService $service;

    private JsonRenderer $renderer;

    public function __construct(AdminScopeService $service, JsonRenderer $jsonRenderer)
    {
        //print_r('text'); exit;

        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }

    //Get All Data
    public function getAllScope(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {

        $scope = $this->service->getAllScope();

        if(!empty($scope)){
            $ret=array("response"=>"success", "data"=>["scope_list"=>$scope]);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No data found');
        }
        
        return $this->renderer->json($response, $ret);

    }

    //Update Role Scope
    public function updateRoleScope(ServerRequestInterface $request, ResponseInterface $response,array $args): ResponseInterface 
    {

        $data = (array)$request->getParsedBody();
        unset($data['payload']);

        $role_id = (int)$args['role-id'];

        $scope = $this->service->updateRoleScope($data, $role_id);

        if($scope == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($scope == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Updated');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }
        
        return $this->renderer->json($response, $ret);

    }

}
