<?php

namespace App\Action\Master_Table\Master_Game_Bonus;

//Data
use App\Domain\Master_Table\Master_Game_Bonus\Data\BonusTypeData;
use App\Domain\Master_Table\Master_Game_Bonus\Data\BonusTypeDataRead;

//Service
use App\Domain\Master_Table\Master_Game_Bonus\Service\BonusTypeService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class BonusTypeAction
{
    private BonusTypeService $service;

    private JsonRenderer $renderer;
    
    public function __construct(BonusTypeService $service, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }

    //Get Data
    public function getBonusType(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
       
        $bonus = $this->service->getBonusType();

        $ret=array("response"=>"success", "data"=>$bonus);
        
        return $this->renderer->json($response, $ret);

    }
    
    //Insert Data
    public function insertBonusType(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

        $data = (array)$request->getParsedBody();
        unset($data['payload']);   
        
        
        $bonustype = $this->service->insertBonusType($data);
        if(!empty($bonustype)){
            $ret=array("response"=>"success", "data"=>["bonustype_id"=>$bonustype]);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No Data Insert');
        }
        
        return $this->renderer->json($response, $ret);
    }    
        
        
    //Update Data
    public function updateBonusType(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
       
        $data=(array)$request->getParsedBody();
        unset($data['payload']);

        $bonustype_id = (int)$args['bonustype-id'];

        $bonustype = $this->service->updateBonusType($bonustype_id, $data);

        if($bonustype == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($bonustype == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Updated');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }         


        return $this->renderer->json($response, $ret);    
      
    }

    //Status Change
    public function bonustypeStatus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {


        $bonustype_Id = (int)$args['bonustype-id'];

        $status = (int)$args['status'];

        $bonustype =$this->service->bonustypeStatus($bonustype_Id, $status);

        if($bonustype == 1){
            $ret=array("response"=>"success", "message"=>'Status Changed Successfully');
        }else if($bonustype == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Update');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }

        return $this->renderer->json($response, $ret);

    }


}
