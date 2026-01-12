<?php

namespace App\Action\Master_Table\Rummy_Format;

//Service
use App\Domain\Master_Table\Rummy_Format\Service\RummyFormatService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class RummyFormatAction
{
    private RummyFormatService $service;

    private JsonRenderer $renderer;

    public function __construct(RummyFormatService $service, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }

    //Get All Data
    public function getRummyFormat(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
      
        $formats = $this->service->getRummyFormat();
        
        $ret = array("response"=>"success", "data"=>$formats);
      
        return $this->renderer->json($response, $ret);
    }  

    //Get Active Data
    public function getActiveRummyFormat(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
      
        $formats = $this->service->getActiveRummyFormat();
        
        $ret = array("response"=>"success", "data"=>$formats);
      
        return $this->renderer->json($response, $ret);
    }  


    //Insert Data
    public function insertRummyFormat(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

        $data = (array)$request->getParsedBody();
        unset($data['payload']);   
     
        $formats = $this->service->insertRummyFormat($data);

        if(!empty($formats)){
            $ret=array("response"=>"success", "data"=>["format-id"=>$formats]);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No Data Insert');
        }
        
        return $this->renderer->json($response, $ret);
    }

    //Update Data
    public function updateRummyFormat(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
       
        $data=(array)$request->getParsedBody();
        unset($data['payload']);

        $format_id = (int)$args['format-id'];
  
        $formats = $this->service->updateRummyFormat($format_id, $data);

        if($formats == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($formats == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Updated');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }    
   
        return $this->renderer->json($response, $ret);    
      
    }


    // Status Change
    public function rummyFormatStatus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {


        $format_id = (int)$args['format-id'];

        $status = (int)$args['status'];

        $formats =$this->service->rummyFormatStatus($format_id, $status);

        if($formats == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($formats == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Update');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }

        return $this->renderer->json($response, $ret);

    }

    //Delete Data
    public function deleteRummyFormat(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $format_id = (int)$args['format-id'];
        $formats = $this->service->deleteRummyFormat($format_id);

        if($formats == 1){
            $ret=array("response"=>"success", "message"=>'Delete Successfully');
        }else if($formats == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Delete');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }

        return $this->renderer->json($response, $ret);

    }

}
