<?php

namespace App\Action\Master_Table\Rummy_Max_Seats;

//Service
use App\Domain\Master_Table\Rummy_Max_Seats\Service\RummyMaxSeatsService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class RummyMaxSeatsAction
{
    private RummyMaxSeatsService $service;

    private JsonRenderer $renderer;
   

    public function __construct(RummyMaxSeatsService $service, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }

    //Get Data
    public function getMaxSeats(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
       
        $max_seats = $this->service->getMaxSeats();

        $ret=array("response"=>"success", "data"=>$max_seats);
        
        return $this->renderer->json($response, $ret);

    }

    public function getActiveMaxSeats(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
       
        $max_seats = $this->service->getActiveMaxSeats();

        $ret=array("response"=>"success", "data"=>$max_seats);
        
        return $this->renderer->json($response, $ret);

    }

    //Insert Data
    public function insertMaxSeats(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

        $data = (array)$request->getParsedBody();
        unset($data['payload']);   
     
        $max_seats = $this->service->insertMaxSeats($data);

        if(!empty($max_seats)){
            $ret=array("response"=>"success", "data"=>["max-seats-id"=>$max_seats]);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No Data Insert');
        }
        
        return $this->renderer->json($response, $ret);
    }

    //Update Data
    public function updateMaxSeats(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
       
        $data=(array)$request->getParsedBody();
        unset($data['payload']);

        $seatsId = (int)$args['seats-id'];
  
        $max_seats = $this->service->updateMaxSeats($seatsId, $data);

        if($max_seats == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($max_seats == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Updated');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }    
   
        return $this->renderer->json($response, $ret);    
      
    }

    //Status Change
    public function maxSeatsStatus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {


        $seatsId = (int)$args['seats-id'];
        $status = (int)$args['status'];

        $max_seats =$this->service->maxSeatsStatus($seatsId, $status);

        if($max_seats == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($max_seats == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Update');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }

        return $this->renderer->json($response, $ret);

    }


    //Delete Data
    public function deleteMaxSeats(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $seatsId = (int)$args['seats-id'];
        $max_seats = $this->service->deleteMaxSeats($seatsId);

        if($max_seats == 1){
            $ret=array("response"=>"success", "message"=>'Delete Successfully');
        }else if($max_seats == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Delete');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }

        return $this->renderer->json($response, $ret);

    }

}
