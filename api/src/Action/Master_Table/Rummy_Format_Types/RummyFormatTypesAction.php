<?php

namespace App\Action\Master_Table\Rummy_Format_Types;

//Service
use App\Domain\Master_Table\Rummy_Format_Types\Service\RummyFormatTypesService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class RummyFormatTypesAction
{
    private RummyFormatTypesService $service;

    private JsonRenderer $renderer;

    public function __construct(RummyFormatTypesService $service, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }

    //Get All Data
    public function getRummyFormatTypes(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface 
    {

        $format_id = (int)$args['format-id'];
      
        $format_types = $this->service->getRummyFormatTypes($format_id);
        
        $ret = array("response"=>"success", "data"=>$format_types);
      
        return $this->renderer->json($response, $ret);
    }  

    //Get Active Data
    public function getActiveRummyFormatTypes(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
      
        $format_types = $this->service->getActiveRummyFormatTypes();
        
        $ret = array("response"=>"success", "data"=>$format_types);
      
        return $this->renderer->json($response, $ret);
    }  


    //Insert Data
    public function insertRummyFormatTypes(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

        $data = (array)$request->getParsedBody();
        unset($data['payload']);   
     
        $format_types = $this->service->insertRummyFormatTypes($data);

        if(!empty($format_types)){
            $ret=array("response"=>"success", "data"=>["format-type-id"=>$format_types]);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No Data Insert');
        }
        
        return $this->renderer->json($response, $ret);
    }

    //Update Data
    public function updateRummyFormatTypes(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
       
        $data=(array)$request->getParsedBody();
        unset($data['payload']);

        $format_type_id = (int)$args['format-type-id'];
  
        $format_types = $this->service->updateRummyFormatTypes($format_type_id, $data);

        if($format_types == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($format_types == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Updated');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }    
   
        return $this->renderer->json($response, $ret);    
      
    }


    // Status Change
    public function rummyFormatTypeStatus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {


        $format_type_id = (int)$args['format-type-id'];

        $status = (int)$args['status'];

        $format_types =$this->service->rummyFormatTypeStatus($format_type_id, $status);

        if($format_types == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($format_types == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Update');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }

        return $this->renderer->json($response, $ret);

    }

    //Delete Data
    public function deleteRummyFormatTypes(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $format_type_id = (int)$args['format-type-id'];
        $format_types = $this->service->deleteRummyFormatTypes($format_type_id);

        if($format_types == 1){
            $ret=array("response"=>"success", "message"=>'Delete Successfully');
        }else if($format_types == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Delete');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }

        return $this->renderer->json($response, $ret);

    }

}
