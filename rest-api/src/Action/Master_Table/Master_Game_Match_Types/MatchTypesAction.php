<?php

namespace App\Action\Master_Table\Master_Game_Match_Types;

//Data
use App\Domain\Master_Table\Master_Game_Match_Types\Data\MatchTypeData;
use App\Domain\Master_Table\Master_Game_Match_Types\Data\MatchTypeDataRead;

//Service
use App\Domain\Master_Table\Master_Game_Match_Types\Service\MatchTypesService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class MatchTypesAction
{
    private MatchTypesService $service;

    private JsonRenderer $renderer;
   

    public function __construct(MatchTypesService $service, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }

    //Get Data
    public function getMatchType(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
       
        $matchtype = $this->service->getMatchType();

        $ret=array("response"=>"success", "data"=>$matchtype);
        
        return $this->renderer->json($response, $ret);

    }

    

    //Insert Data
    public function insertMatchType(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

        $data = (array)$request->getParsedBody();
        unset($data['payload']);   
     
        $matchtype = $this->service->insertMatchType($data);

        if(!empty($matchtype)){
            $ret=array("response"=>"success", "data"=>["admin-id"=>$matchtype]);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No Data Insert');
        }
        
        return $this->renderer->json($response, $ret);
    }

    //Update Data
    public function updateMatchType(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
       
        $data=(array)$request->getParsedBody();
        unset($data['payload']);

        $matchtype_id = (int)$args['matchtype-id'];
  
        $matchtype = $this->service->updateMatchType($matchtype_id, $data);

        if($matchtype == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($matchtype == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Updated');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }    
   
        return $this->renderer->json($response, $ret);    
      
    }

    //Status Change
    public function matchStatus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {


        $match_Id = (int)$args['matchtype-id'];

        $status = (int)$args['status'];

        $matchtype =$this->service->matchStatus($match_Id, $status);

        if($matchtype == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($matchtype == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Update');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }

        return $this->renderer->json($response, $ret);

    }


    //Delete Data
    public function deleteMatchType(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $matchtype_Id = (int)$args['matchtype-id'];
        $matchtype = $this->service->deleteMatchType($matchtype_Id);

        if($matchtype == 1){
            $ret=array("response"=>"success", "message"=>'Delete Successfully');
        }else if($matchtype == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Delete');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }

        return $this->renderer->json($response, $ret);

    }

}
