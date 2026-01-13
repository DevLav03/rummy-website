<?php

namespace App\Action\Rummy_Game\Deposit;

//Service
use App\Domain\Rummy_Game\Deposit\Service\DepositService;
use App\Domain\Admin_Panel\Users\Service\UsersService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class DepositAction
{
    private DepositService $service;
    private UsersService $userservice;

    private JsonRenderer $renderer;

    public function __construct(DepositService $service, UsersService $userservice, JsonRenderer $jsonRenderer) 
    {
        $this->service = $service;
        $this->userservice = $userservice;
        $this->renderer = $jsonRenderer;
    }

    //Get All Data
    public function createOrder(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {

        $data = (array)$request->getParsedBody();
        $user_id = $data['payload']['id'];
        unset($data['payload']);

        //$users = $this->userservice->getOneUser($user_id); 
        $deposit = $this->service->createOrder($data, $user_id);
        
        if($deposit==1){
            $ret=array("response"=>"success", "message"=>"Data Insert Succesfully"); 
        }else{
            $ret=array("response"=>"failure", "err_message"=>"Insert Failed");
        }
        return $this->renderer->json($response, $ret);
    }   

 
}
?>       