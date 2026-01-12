<?php

namespace App\Action\Master_Table\Game_Welcome_Bonus;

//Data
use App\Domain\Master_Table\Game_Welcome_Bonus\Data\WelcomeBonusData;
use App\Domain\Master_Table\Game_Welcome_Bonus\Data\WelcomeBonusDataRead;

//Service
use App\Domain\Master_Table\Game_Welcome_Bonus\Service\WelcomeBonusService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class WelcomeBonusAction
{
    private WelcomeBonusService $service;

    private JsonRenderer $renderer;
    
    public function __construct(WelcomeBonusService $service, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }

    //Get Data
    public function getWelcomeBonus(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
       
        $welcome = $this->service->getWelcomeBonus();

        $ret=array("response"=>"success", "data"=>$welcome);
        
        return $this->renderer->json($response, $ret);

    }
    
    //Insert Data
    public function insertWelcomeBonus(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

        $data = (array)$request->getParsedBody();
        unset($data['payload']);   
        
        
        $welcome = $this->service->insertWelcomeBonus($data);
        if(!empty($welcome)){
            $ret=array("response"=>"success", "data"=>["welcomebonus_id"=>$welcome]);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No Data Insert');
        }
        
        return $this->renderer->json($response, $ret);
    }    
        
        
    //Update Data
    public function updateWelcomeBonus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
       
        $data=(array)$request->getParsedBody();
        unset($data['payload']);

        $welcomebonus_id = (int)$args['welcomebonus-id'];

        $welcome = $this->service->updateWelcomeBonus($welcomebonus_id, $data);

        if($welcome == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($welcome == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Updated');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }         


        return $this->renderer->json($response, $ret);    
      
    }

    //Delete Data
    public function deleteWelcomeBonus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $welcomebonus_Id = (int)$args['welcomebonus-id'];
        $welcome = $this->service->deleteWelcomeBonus($welcomebonus_Id);

        if($welcome == 0){
            $ret=array("response"=>"success", "message"=>'Delete Successfully');
        }else if($welcome == 1){
            $ret=array("response"=>"failure", "err_message"=>'No Record Delete');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }

        return $this->renderer->json($response, $ret);

    }


}
