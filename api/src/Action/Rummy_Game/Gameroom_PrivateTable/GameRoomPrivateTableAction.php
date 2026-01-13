<?php

namespace App\Action\Rummy_Game\Gameroom_PrivateTable;

//Service
use App\Domain\Rummy_Game\Gameroom_PrivateTable\Service\GameRoomPrivateTableService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class GameRoomPrivateTableAction
{
    private GameRoomPrivateTableService $service;
    

    private JsonRenderer $renderer;

    public function __construct(GameRoomPrivateTableService $service, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }



   //Insert Data
   public function insertPrivateTable(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
   {

       $data = (array)$request->getParsedBody();
       $user_id = $data['payload']['id'];
       unset($data['payload']);   
       
       $privatetable = $this->service->insertPrivateTable($data, $user_id);

        if(!empty($privatetable)){
            $ret=array("response"=>"success", "data"=>["code"=>$privatetable]);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No Data Insert');
        }

       
       return $this->renderer->json($response, $ret);
    }  
    

    //Enter Private Table
    public function enterPrivateTable(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {

        $data = (array)$request->getParsedBody();
        unset($data['payload']);

        $privatetable = $this->service->enterPrivateTable($data);
        
        
        if(!empty($privatetable == 1)){
            $ret=array("response"=>"success", "message"=>"Successfully"); 
        }else if($privatetable == 0){
            $ret=array("response"=>"failure", "err_message"=>'Not Accepted this code');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }         
        
        return $this->renderer->json($response, $ret);

    } 
   
    
    
}
