<?php

namespace App\Action\Rummy_Game\GameRoom_Tournament;

//Service
use App\Domain\Rummy_Game\GameRoom_Tournament\Service\GameRoomTournamentService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class GameRoomTournamentAction
{
    private GameRoomTournamentService $service;
    

    private JsonRenderer $renderer;

    public function __construct(GameRoomTournamentService $service, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }

    public function getTourneyGameType(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
        $data = $this->service->getTourneyGameType();

        if(!empty($data)){
            $ret=array("response"=>"success", "data"=>$data);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No data found');
        }
        
        return $this->renderer->json($response, $ret);
    }  
    
    public function getTourneyMaxPlayer(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface 
    {
        $game_id = (int)$args['game-id'];

        $data = $this->service->getTourneyMaxPlayer($game_id);

        if(!empty($data)){
            $ret=array("response"=>"success", "data"=>$data);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No data found');
        }
        
        return $this->renderer->json($response, $ret);
    }  

    public function getTourneyEntryFees(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface 
    {
        $game_id = (int)$args['game-id'];
        $max_player = (int)$args['max-player'];

        $data = $this->service->getTourneyEntryFees($game_id, $max_player);

        if(!empty($data)){
            $ret=array("response"=>"success", "data"=>$data);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No data found');
        }
        
        return $this->renderer->json($response, $ret);
    }  

    //Get All Data
    public function getTourneyroom(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {

        $data = (array)$request->getParsedBody();
        unset($data['payload']);
       
        $freegame = $this->service->getTourneyroom($data);
        //print_r($data); exit;
        if(!empty($freegame)){
            $ret=array("response"=>"success", "data"=>["tourney_room"=>$freegame, "total"=>$freegame[0]['total']]);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No data found');
        }
        
        return $this->renderer->json($response, $ret);

    }   

   //Insert Data
   public function insertTourneyroom(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
   {

       $data = (array)$request->getParsedBody();
       unset($data['payload']);   
       
       $tourneygame = $this->service->insertTourneyroom($data);

        if(!empty($tourneygame)){
            $ret=array("response"=>"success", "data"=>["tourneygame_id"=>$tourneygame]);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No Data Insert');
        }

       
       return $this->renderer->json($response, $ret);
    }  
    
    
    

    //Active Change
    public function TourneyroomActive(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {


        $tourneygame_Id = (int)$args['tourneygame-id'];

        $active = (int)$args['active'];

        $tourneygame =$this->service->TourneyroomActive($tourneygame_Id, $active);

        if($tourneygame == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($tourneygame == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Update');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }

        return $this->renderer->json($response, $ret);

    }


    //Delete Data
    public function deleteTourneyroom(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $tourneygame_id = (int)$args['tourneygame-id'];
        $tourneygame = $this->service->deleteTourneyroom($tourneygame_id);

        if($tourneygame == 1){
            $ret=array("response"=>"success", "message"=>'Delete Successfully');
        }else if($tourneygame == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Delete');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }

        return $this->renderer->json($response, $ret);

    }
}
