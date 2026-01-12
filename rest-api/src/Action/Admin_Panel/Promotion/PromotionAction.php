<?php

namespace App\Action\Admin_Panel\Promotion;

//Data
use App\Domain\Admin_Panel\Promotion\Data\PromotionData;
use App\Domain\Admin_Panel\Promotion\Data\PromotionDataRead;

//Service
use App\Domain\Admin_Panel\Promotion\Service\PromotionService;


use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class PromotionAction
{
    private PromotionService $service;
    

    private JsonRenderer $renderer;

    public function __construct(PromotionService $service, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
       
        $this->renderer = $jsonRenderer;
    }

    //Get All Data
    public function getPromotion(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
       
        $promotions = $this->service->getPromotion();

        $ret=array("response"=>"success", "data"=>$promotions);
        
        return $this->renderer->json($response, $ret);

    }

  
    //Get One Data
    public function getOnePromotion(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface { 

        $promotionId = (int)$args['promotion-id'];
        
        $promotion = $this->service->getOnePromotion($promotionId); 

        if(!empty($promotion)){

            $result = new PromotionData();

            foreach ($promotion as $promotionRow) {
    
                $promotion = new PromotionDataRead();
    
                $promotion->id = $promotionRow['id'];
                $promotion->title = $promotionRow['title'];
                $promotion->short_description = $promotionRow['short_description'];
                $promotion->description = $promotionRow['description'];
                $promotion->promotion_image = $promotionRow['promotion_image'];
                $promotion->status = $promotionRow['status'];
                $promotion->created_at = $promotionRow['created_at'];
    
                $result->promotion[] = $promotion;
            }

            $ret=array("response"=>"success", "data"=>$result);

        }else{

            $ret=array("response"=>"failure", "err_message"=>'No data found');

        }

        return $this->renderer->json($response, $ret);
    } 


    //Insert Data
    public function insertPromotion(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

        $data = (array)$request->getParsedBody();

        $uploadedFiles = $request->getUploadedFiles();
        unset($data['payload']); 
        
        if (array_key_exists('promotion_image', $uploadedFiles)) {
            $file = $uploadedFiles['promotion_image']; 
            $promoimgInsert = $this->service->insertPromotion($data, $file);
        }
        else{
            $promoimgInsert = $this->service->insertPromotion($data, null);
        } 
       
        if($promoimgInsert == 0){
            $ret=array("response"=>"success", "message"=>'Insert Successfully');
        }else if($promoimgInsert == 1){
            $ret=array("response"=>"failure", "err_message"=>'Upload File Error');
        }else if($promoimgInsert == 2){
            $ret=array("response"=>"failure", "err_message"=>'Upload File Size Limit 2mb');
        }else if($promoimgInsert == 3){
            $ret=array("response"=>"failure", "err_message"=>'Updalod File Type png,jpg,jpeg');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No Data Insert');
        }
    
        return $this->renderer->json($response, $ret);
    }

   
    //Update Data
    public function updatePromotion(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {

        $data = (array)$request->getParsedBody();
        $promotion_id = (int)$args['promotion-id'];
        $uploadedFiles = $request->getUploadedFiles();
        unset($data['payload']); 
        
        //print_r($promotion_id); exit;
        if (array_key_exists('promotion_image', $uploadedFiles)) {
            $file = $uploadedFiles['promotion_image']; 
            $promoimgInsert = $this->service->updatePromotion($data, $promotion_id, $file);
        }
        else{
            $promoimgInsert = $this->service->updatePromotion($data,$promotion_id, null);
        } 
    
        if($promoimgInsert == 0){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($promoimgInsert == 1){
            $ret=array("response"=>"failure", "err_message"=>'Upload File Error');
        }else if($promoimgInsert == 2){
            $ret=array("response"=>"failure", "err_message"=>'Upload File Size Limit 2mb');
        }else if($promoimgInsert == 3){
            $ret=array("response"=>"failure", "err_message"=>'Updalod File Type png,jpg,jpeg');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No Data Update');
        }
    
        return $this->renderer->json($response, $ret);
    }


    //Delete Data
    public function deletePromotion(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $promotion_Id = (int)$args['promotion-id'];
        $promotion = $this->service->deletePromotion($promotion_Id);

        if($promotion == 1){
            $ret=array("response"=>"success", "message"=>'Delete Successfully');
        }else if($promotion == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Delete');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }

        return $this->renderer->json($response, $ret);

    }

    //Change Status
    public function promotionStatus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {


        $promotion_Id = (int)$args['promotion-id'];

        $status = (int)$args['status'];

        $promotion =$this->service->promotionStatus($promotion_Id, $status);

        if($promotion == 1){
            $ret=array("response"=>"success", "message"=>'Status Changed Successfully');
        }else if($promotion == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Update');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }

        return $this->renderer->json($response, $ret);

    }
}
