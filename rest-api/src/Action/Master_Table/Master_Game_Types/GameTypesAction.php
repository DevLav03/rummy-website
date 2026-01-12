<?php

namespace App\Action\Master_Table\Master_Game_Types;

//Service
use App\Domain\Master_Table\Master_Game_Types\Service\GameTypesService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class GameTypesAction
{
    private GameTypesService $service;

    private JsonRenderer $renderer;

    public function __construct(GameTypesService $service, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }

    //Get Data
    public function getGameTypes(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
      
        $gameTypes = $this->service->getGameTypes();
        
        $ret = array("response"=>"success", "data"=>$gameTypes);
      
        return $this->renderer->json($response, $ret);
    }  


    //Insert Data
    public function insertGameType(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

        $data = (array)$request->getParsedBody();
        unset($data['payload']);   
     
        $gametype = $this->service->insertGameType($data);

        if(!empty($gametype)){
            $ret=array("response"=>"success", "data"=>["gametype-id"=>$gametype]);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No Data Insert');
        }
        
        return $this->renderer->json($response, $ret);
    }

    //Update Data
    public function updateGameType(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
       
        $data=(array)$request->getParsedBody();
        unset($data['payload']);

        $game_id = (int)$args['game-id'];
  
        $gametype = $this->service->updateGameType($game_id, $data);

        if($gametype == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($gametype == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Updated');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }    
   
        return $this->renderer->json($response, $ret);    
      
    }


    // Status Change
    public function gameStatus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {


        $game_Id = (int)$args['game-id'];

        $status = (int)$args['status'];

        $gametype =$this->service->gameStatus($game_Id, $status);

        if($gametype == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($gametype == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Update');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }

        return $this->renderer->json($response, $ret);

    }

    //Delete Data
    public function deleteGameType(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $game_Id = (int)$args['game-id'];
        $gametype = $this->service->deleteGameType($game_Id);

        if($gametype == 1){
            $ret=array("response"=>"success", "message"=>'Delete Successfully');
        }else if($gametype == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Delete');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }

        return $this->renderer->json($response, $ret);

    }

}
