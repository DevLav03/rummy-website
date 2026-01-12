<?php

namespace App\Action\Rummy_Game\GameRoom_Free;

//Service
use App\Domain\Rummy_Game\Gameroom_Free\Service\GameRoomFreeService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class GameRoomFreeAction
{
    private GameRoomFreeService $service;
    

    private JsonRenderer $renderer;

    public function __construct(GameRoomFreeService $service, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }

    //Get All Data
    public function getGamefreeroom(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {

        $data = (array)$request->getParsedBody();
        unset($data['payload']);
       
        $freegame = $this->service->getGamefreeroom($data);

        if(!empty($freegame)){
            $ret=array("response"=>"success", "data"=>["free_money_table"=>$freegame, "total"=>$freegame[0]['total']]);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No data found');
        }
        
        return $this->renderer->json($response, $ret);

    }   



   //Insert Data
   public function insertfreeGameroom(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
   {

       $data = (array)$request->getParsedBody();
       unset($data['payload']);   
       
       $freegame = $this->service->insertfreeGameroom($data);

        if(!empty($freegame)){
            $ret=array("response"=>"success", "data"=>["freegame_id"=>$freegame]);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No Data Insert');
        }

       
       return $this->renderer->json($response, $ret);
    }  
    
    
    //Update Data
    public function updatefreeGameroom(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
       
        $data=(array)$request->getParsedBody();
        unset($data['payload']);

        $freegame_id = (int)$args['freegame-id'];

       
        $freegame = $this->service->updatefreeGameroom($freegame_id, $data);

        if($freegame == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($freegame == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Updated');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }         
       
        return $this->renderer->json($response, $ret);    
      
    }

    //Status Change
    public function gamefreeroomStatus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {


        $freegame_Id = (int)$args['freegame-id'];

        $status = (int)$args['status'];

        $freegame =$this->service->gamefreeroomStatus($freegame_Id, $status);

        if($freegame == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($freegame == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Update');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }

        return $this->renderer->json($response, $ret);

    }


    //Delete Data
    public function deletefreeGameroom(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $freegame_id = (int)$args['freegame-id'];
        $freegame = $this->service->deletefreeGameroom($freegame_id);

        if($freegame == 1){
            $ret=array("response"=>"success", "message"=>'Delete Successfully');
        }else if($freegame == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Delete');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }

        return $this->renderer->json($response, $ret);

    }
}
