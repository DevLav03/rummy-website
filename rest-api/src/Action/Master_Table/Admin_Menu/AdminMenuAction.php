<?php

namespace App\Action\Master_Table\Admin_Menu;

//Service
use App\Domain\Master_Table\Admin_Menu\Service\AdminMenuService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class AdminMenuAction
{
    private AdminMenuService $service;

    private JsonRenderer $renderer;

    public function __construct(AdminMenuService $service, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }

    //Get All Data
    public function getAllMenu(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {

        $menu = $this->service->getAllMenu();

        if(!empty($menu)){
            $ret=array("response"=>"success", "data"=>["menu_list"=>$menu]);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No data found');
        }
        
        return $this->renderer->json($response, $ret);

    }


    public function getMenu(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {

        $data = (array)$request->getParsedBody();
        unset($data['payload']);
       
        $menu = $this->service->getMenu($data);

        if(!empty($menu)){
            $ret=array("response"=>"success", "data"=>["menu_list"=>$menu]);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No data found');
        }
        
        return $this->renderer->json($response, $ret);

    }

    //Update Data
    public function updateMenu(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
       
        $data=(array)$request->getParsedBody();
        unset($data['payload']);

        $menu_id = (int)$args['menu-id'];
 
        //$data['password'] = $password;

        $menu = $this->service->updateMenu($menu_id, $data);

        if($menu == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($menu == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Updated');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }
                  

        return $this->renderer->json($response, $ret);    
      
    }

    public function menuStatus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $menu_id = (int)$args['menu-id'];
        $status = (int)$args['status'];
 
        //$data['password'] = $password;

        $menu = $this->service->menuStatus($menu_id, $status);

        if($menu == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($menu == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Updated');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }
                  

        return $this->renderer->json($response, $ret);    
      
    }

}
