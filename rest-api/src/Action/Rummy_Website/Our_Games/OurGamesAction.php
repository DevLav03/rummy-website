<?php

namespace App\Action\Rummy_Website\Our_Games;

//Service
use App\Domain\Rummy_Website\Our_Games\Service\OurGamesService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class OurGamesAction
{
    private OurGamesService $service;
    private JsonRenderer $renderer;

    public function __construct(OurGamesService $service, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }

    //Get All Data
    public function getAllGames(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
       
        $games = $this->service->getAllGames();

        $ret=array("response"=>"success", "data"=>$games);
        
        return $this->renderer->json($response, $ret);

    }

    //Get  Data
    public function getGames(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
       
        $games = $this->service->getGames();

        $ret=array("response"=>"success", "data"=>$games);
        
        return $this->renderer->json($response, $ret);

    }

    //Insert Data
    public function insertGames(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

        $data = (array)$request->getParsedBody();
        $uploadedFiles = $request->getUploadedFiles();
        unset($data['payload']); 

        
        
        if (array_key_exists('image', $uploadedFiles)) {
            $file = $uploadedFiles['image']; 
            $imageInsert = $this->service->insertGames($data, $file);
        }
        else{
            $imageInsert = $this->service->insertGames($data, null);
        } 
       
        if($imageInsert == 0){
            $ret=array("response"=>"success", "message"=>'Insert Successfully');
        }else if($imageInsert == 1){
            $ret=array("response"=>"failure", "err_message"=>'Upload File Error');
        }else if($imageInsert == 2){
            $ret=array("response"=>"failure", "err_message"=>'Upload File Size Limit 2mb');
        }else if($imageInsert == 3){
            $ret=array("response"=>"failure", "err_message"=>'Updalod File Type png,jpg,jpeg');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No Data Insert');
        }
    
        return $this->renderer->json($response, $ret);
    }
    
    //Update Data
    public function updateGames(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {

        $data = (array)$request->getParsedBody();
        $games_id = (int)$args['games-id'];
        $uploadedFiles = $request->getUploadedFiles();
        unset($data['payload']); 
        
        
        if (array_key_exists('images', $uploadedFiles)) {
            $file = $uploadedFiles['images']; 
            $imageInsert = $this->service->updateGames($data, $games_id, $file);
        }
        else{
            $imageInsert = $this->service->updateGames($data, $games_id, null);
        } 
    
        if($imageInsert == 0){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($imageInsert == 1){
            $ret=array("response"=>"failure", "err_message"=>'Upload File Error');
        }else if($imageInsert == 2){
            $ret=array("response"=>"failure", "err_message"=>'Upload File Size Limit 2mb');
        }else if($imageInsert == 3){
            $ret=array("response"=>"failure", "err_message"=>'Updalod File Type png,jpg,jpeg'); 
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No Data Update');
        }
    
        return $this->renderer->json($response, $ret);
    }

    //status change
    public function changeStatus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $games_id = (int)$args['games-id'];
        $status = (int)$args['status'];
       
        $games = $this->service->changeStatus($games_id, $status);

        if($games == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($games == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Updated');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }
        
        return $this->renderer->json($response, $ret);

    }

    //Delete Data
    public function deleteGames(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $games_Id = (int)$args['games-id'];
        $games = $this->service->deleteGames($games_Id);

        if($games == 1){
            $ret=array("response"=>"success", "message"=>'Delete Successfully');
        }else if($games == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Delete');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }

        return $this->renderer->json($response, $ret);

    }

}
