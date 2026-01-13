<?php

namespace App\Action\Master_Table\Rummy_Variants;

//Service
use App\Domain\Master_Table\Rummy_Variants\Service\RummyVariantsService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class RummyVariantsAction
{
    private RummyVariantsService $service;

    private JsonRenderer $renderer;
   

    public function __construct(RummyVariantsService $service, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }

    //Get Data
    public function getRummyVariants(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
       
        $variants = $this->service->getRummyVariants();

        $ret=array("response"=>"success", "data"=>$variants);
        
        return $this->renderer->json($response, $ret);

    }

    public function getActiveRummyVariants(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
       
        $variants = $this->service->getActiveRummyVariants();

        $ret=array("response"=>"success", "data"=>$variants);
        
        return $this->renderer->json($response, $ret);

    }

    //Insert Data
    public function insertRummyVariants(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

        $data = (array)$request->getParsedBody();
        unset($data['payload']);   
     
        $variants = $this->service->insertRummyVariants($data);

        if(!empty($variants)){
            $ret=array("response"=>"success", "data"=>["variants-id"=>$variants]);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No Data Insert');
        }
        
        return $this->renderer->json($response, $ret);
    }

    //Update Data
    public function updateRummyVariants(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
       
        $data=(array)$request->getParsedBody();
        unset($data['payload']);

        $variants_id = (int)$args['variants-id'];
  
        $variants = $this->service->updateRummyVariants($variants_id, $data);

        if($variants == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($variants == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Updated');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }    
   
        return $this->renderer->json($response, $ret);    
      
    }

    //Status Change
    public function rummyVariantStatus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {


        $variants_id = (int)$args['variants-id'];
        $status = (int)$args['status'];

        $variants =$this->service->rummyVariantStatus($variants_id, $status);

        if($variants == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($variants == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Update');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }

        return $this->renderer->json($response, $ret);

    }


    //Delete Data
    public function deleteRummyVariants(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $variants_id = (int)$args['variants-id'];
        $variants = $this->service->deleteRummyVariants($variants_id);

        if($variants == 1){
            $ret=array("response"=>"success", "message"=>'Delete Successfully');
        }else if($variants == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Delete');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }

        return $this->renderer->json($response, $ret);

    }

}
