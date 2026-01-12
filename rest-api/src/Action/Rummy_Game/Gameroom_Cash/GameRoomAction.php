<?php

namespace App\Action\Rummy_Game\GameRoom_Cash;

//Service
use App\Domain\Rummy_Game\Gameroom_Cash\Service\GameRoomService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class GameRoomAction
{
    private GameRoomService $service;
    

    private JsonRenderer $renderer;

    public function __construct(GameRoomService $service, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }

    //Get cash gametype
    public function getCashGameType(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
        $data = $this->service->getCashGameType();

        if(!empty($data)){
            $ret=array("response"=>"success", "data"=>$data);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No data found');
        }
        
        return $this->renderer->json($response, $ret);
    } 

    //get max player
    public function getCashMaxPlayer(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface 
    {
        $game_id = (int)$args['game-id'];

        $data = $this->service->getCashMaxPlayer($game_id);

        if(!empty($data)){
            $ret=array("response"=>"success", "data"=>$data);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No data found');
        }
        
        return $this->renderer->json($response, $ret);
    } 

    //Get Entry Fees
    public function getCashEntryFees(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface 
    {
        $game_id = (int)$args['game-id'];
        $max_player = (int)$args['max-player'];

        $data = $this->service->getCashEntryFees($game_id, $max_player);

        if(!empty($data)){
            $ret=array("response"=>"success", "data"=>$data);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No data found');
        }
        
        return $this->renderer->json($response, $ret);
    } 
    //Get All Data
    public function getGameroom(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {

        $data = (array)$request->getParsedBody();
        unset($data['payload']);
       
        $gameroom = $this->service->getGameroom($data);

        if(!empty($gameroom)){
            $ret=array("response"=>"success", "data"=>["real_money_table"=>$gameroom, "total"=>$gameroom[0]['total']]);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No data found');
        }
        
        return $this->renderer->json($response, $ret);

    }   



   //Insert Data
   public function insertGameroom(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
   {

       $data = (array)$request->getParsedBody();
       unset($data['payload']);   

       $table_count = $this->service->getTableCount($data);

       if($table_count == 0){

            $gameroom = $this->service->insertGameroom($data);

            if(!empty($gameroom)){  
                $ret=array("response"=>"success", "data"=>["gameroom_id"=>$gameroom]);
            }else{
                $ret=array("response"=>"failure", "err_message"=>'No Data Insert');
            }
  
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Game Room is Already Exists');
        }  

       return $this->renderer->json($response, $ret);
    }  
    
    
    //Update Data
    public function updateGameroom(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
       
        $data=(array)$request->getParsedBody();
        unset($data['payload']);

        $gameroom_id = (int)$args['gameroom-id'];

       
        $gameroom = $this->service->updateGameroom($gameroom_id, $data);

        if($gameroom == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($gameroom == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Updated');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }         
       
        return $this->renderer->json($response, $ret);    
      
    }

    //Status Change
    public function gameroomStatus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {


        $gameroom_Id = (int)$args['gameroom-id'];

        $status = (int)$args['status'];

        $gameroom =$this->service->gameroomStatus($gameroom_Id, $status);

        if($gameroom == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($gameroom == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Update');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }

        return $this->renderer->json($response, $ret);

    }


    //Delete Data
    public function deleteGameroom(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $gameroom_Id = (int)$args['gameroom-id'];
        $gameroom = $this->service->deleteGameroom($gameroom_Id);

        if($gameroom == 1){
            $ret=array("response"=>"success", "message"=>'Delete Successfully');
        }else if($gameroom == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Delete');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }

        return $this->renderer->json($response, $ret);

    }
}
