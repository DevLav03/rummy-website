<?php

namespace App\Action\Admin_Panel\Users;

//Service
use App\Domain\Admin_Panel\Users\Service\UsersService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class UsersAction
{
    private UsersService $service;
    private JsonRenderer $renderer;

    public function __construct(UsersService $service, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }

    //Get Data
    public function getUsers(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
        $users = $this->service->getUsers();

        $ret=array("response"=>"success", "data"=>$users);
      
        return $this->renderer->json($response, $ret);
    }


    //Get One Data
    public function getOneUser(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface { 

        $userId = (int)$args['user-id'];
        $users = $this->service->getOneUser($userId); 

        if(!empty($users)){        
            $ret=array("response"=>"success", "data"=>$users);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No data found');
        }

        return $this->renderer->json($response, $ret);
    }
                     

    //Get Cash Chips
    public function getCashChips(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface { 

        $userId = (int)$args['user-id'];
        $users = $this->service->getCashChips($userId); 

        if(!empty($users)){        
            $ret=array("response"=>"success", "data"=>$users);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No data found');
        }

        return $this->renderer->json($response, $ret);
    }

    //Get Free Chips
    public function getFreeChips(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface { 

        $userId = (int)$args['user-id'];
        $users = $this->service->getFreeChips($userId); 

        if(!empty($users)){        
            $ret=array("response"=>"success", "data"=>$users);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No data found');
        }

        return $this->renderer->json($response, $ret);
    }
    
    //Get Bonus
    public function getBonus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface { 

        $userId = (int)$args['user-id'];
        $users = $this->service->getBonus($userId); 

        if(!empty($users)){        
            $ret=array("response"=>"success", "data"=>$users);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No data found');
        }

        return $this->renderer->json($response, $ret);
    }


    //Get Points
    public function getPoints(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface { 

        $userId = (int)$args['user-id'];
        $users = $this->service->getPoints($userId); 

        if(!empty($users)){        
            $ret=array("response"=>"success", "data"=>$users);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No data found');
        }

        return $this->renderer->json($response, $ret);
    }

    //Get Game Details
    public function usersGameDetails(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface { 

        $userId = (int)$args['user-id'];
        $users = $this->service->usersGameDetails($userId); 

        if(!empty($users)){        
            $ret=array("response"=>"success", "data"=>$users);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No data found');
        }

        return $this->renderer->json($response, $ret);
    }

    //Change Active Status
    public function activeUser(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface { 
        
        $user_id = (int)$args['user-id'];
        $active = (int)$args['active'];

        $users = $this->service->activeUser($user_id, $active);

        if($users == 1){
            $ret=array("response"=>"success", "message"=>'Updated Successfully');
        }else if($users == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Data Updated');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went wrong');
        }
        
        return $this->renderer->json($response, $ret);
      
    }

    //Users Log History
    public function userLogHistory(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $user_id = (int)$args['user-id'];

        $data = (array)$request->getParsedBody();
        unset($data['payload']); 
        
        $users =$this->service->userLogHistory($data, $user_id);
        $total =$this->service->userLogHistoryCount($data, $user_id);

        if(!empty($users)){
            $ret=array("response"=>"success", "data"=>[$users,['total'=>$total]]);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No Data Found');
        }

        return $this->renderer->json($response, $ret);
      
   
    }



}


