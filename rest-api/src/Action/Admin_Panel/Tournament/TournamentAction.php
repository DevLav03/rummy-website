<?php

namespace App\Action\Admin_Panel\Tournament;

//Service
use App\Domain\Admin_Panel\Tournament\Service\TournamentService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class TournamentAction
{
    private TournamentService $service;

    private JsonRenderer $renderer;
    private $tournament_id;

    public function __construct(TournamentService $service, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }

    //Get All Tournaments 
    public function getTournaments(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $data = (array)$request->getParsedBody();
        unset($data['payload']); 
        
        $tournaments =$this->service->getTournaments($data);
        $total =$this->service->getTournamentsCount($data);

        if(!empty($tournaments)){
            $ret=array("response"=>"success", "data"=>[$tournaments,['total'=>$total]]);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No Data Found');
        }

        return $this->renderer->json($response, $ret);
      
   
    }
 
    //Get One Data
    public function getOneTournament(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface { 

        $tournamentId = (int)$args['tournament-id'];

        $tournaments = $this->service->getOneTournament($tournamentId); 

        if(!empty($tournaments)){

            $ret=array("response"=>"success", "data"=>$tournaments);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No Data Found');
        }

        return $this->renderer->json($response, $ret);
    }

    //Insert Data
    public function insertTournament(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        unset($data['payload']);
       
        $tournament = $this->service->insertTournament($data);

        if(!empty($tournament)){
            $ret=array("response"=>"success", "data"=>["tournament-id"=>$tournament]);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No Data Insert');
        }
  
        return $this->renderer->json($response, $ret);
    }

    //Update Data
    public function updateTournament(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
       
        $data=(array)$request->getParsedBody();

        $tournament_id = (int)$args['tournament-id'];

        $tournament =$this->service->updateTournament($tournament_id, $data);

    
        if($tournament == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($tournament == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Updated');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }

        return $this->renderer->json($response, $ret);
      
    }


    //block and unblock
    public function blockTournament(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $tournament_id = (int)$args['tournament-id'];
        $tourney_status = (int)$args['tourney_status'];

        $tournament =$this->service->blockTournament($tournament_id,$tourney_status);

       
        if($tournament == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($tournament == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Updated');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');

        }
        return $this->renderer->json($response, $ret);

    }
    

}
