<?php

namespace App\Action\Rummy_Website\FAQ;

//Service
use App\Domain\Rummy_Website\FAQ\Service\FaqService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class FaqAction
{
    private FaqService $service;
    private JsonRenderer $renderer;

    public function __construct(FaqService $service, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }

    //Get All Data
    public function getAllFaq(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
       
        $faq = $this->service->getAllFaq();

        $ret=array("response"=>"success", "data"=>$faq);
        
        return $this->renderer->json($response, $ret);

    }

    //get data
    public function getFaq(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
       
        $faq = $this->service->getFaq();

        $ret=array("response"=>"success", "data"=>$faq);
        
        return $this->renderer->json($response, $ret);

    }

    //Get Latest Data
    public function getLatestFaq(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
       
        $faq = $this->service->getLatestFaq();

        $ret=array("response"=>"success", "data"=>$faq);
        
        return $this->renderer->json($response, $ret);

    }

    //Insert Data
    public function insertFaq(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

        $data = (array)$request->getParsedBody();
        unset($data['payload']);   

        $faq = $this->service->insertFaq($data);

        if(!empty($faq)){
            $ret=array("response"=>"success", "data"=>["faq-id"=>$faq]);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No Data Insert');
        }

        
        return $this->renderer->json($response, $ret);
    }



    //Update Data
    public function updateFaq(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
       
        $data=(array)$request->getParsedBody();
        unset($data['payload']);

        $faq_id = (int)$args['faq-id'];
        
        
        $faq = $this->service->updateFaq($faq_id, $data);

        if($faq == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($faq == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Updated');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }
        
    
        return $this->renderer->json($response, $ret);    
      
    }
    

    //status change
    public function changeStatus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $faq_id = (int)$args['faq-id'];
        $status = (int)$args['status'];

        $faq = $this->service->changeStatus($faq_id, $status);

        if($faq == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($faq == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Updated');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }
        
        return $this->renderer->json($response, $ret);

    }

    //Delete Data
    public function deleteFaq(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $faq_Id = (int)$args['faq-id'];
        $faq = $this->service->deleteFaq($faq_Id);

        if($faq == 1){
            $ret=array("response"=>"success", "message"=>'Delete Successfully');
        }else if($faq == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Delete');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }

        return $this->renderer->json($response, $ret);

    }

}
