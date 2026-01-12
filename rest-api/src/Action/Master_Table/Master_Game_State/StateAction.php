<?php

namespace App\Action\Master_Table\Master_Game_State;

//Service
use App\Domain\Master_Table\Master_Game_State\Service\StateService;


use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class StateAction
{
    private StateService $service;
    

    private JsonRenderer $renderer;

    public function __construct(StateService $service, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }

    //Get All Data
    public function getStates(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
       
        $state = $this->service->getStates();

        $ret=array("response"=>"success", "data"=>$state);
        
        return $this->renderer->json($response, $ret);

    }

    //Get One Data
    public function getOneState(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface { 

        $stateId = (int)$args['state-id'];
        
        $state = $this->service->getOneState($stateId); 

        if(!empty($state)){

            $ret=array("response"=>"success", "data"=>$state);

        }else{

            $ret=array("response"=>"failure", "err_message"=>'No data found');

        }

        return $this->renderer->json($response, $ret);
    } 


    //Update Data
    public function updateState(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
       
        $data=(array)$request->getParsedBody();
        unset($data['payload']);

        $state_id = (int)$args['state-id'];
        
        $state = $this->service->updateState($state_id, $data);

        if($state == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($state == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Updated');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }
 
        return $this->renderer->json($response, $ret);    
      
    }


    //Change status 
    public function ChangestateStatus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {


        $state_id = (int)$args['state-id'];

        $status = (int)$args['status'];

        $state =$this->service->ChangestateStatus($state_id, $status);

        if($state == 1){
            $ret=array("response"=>"success", "message"=>'Status Changed Successfully');
        }else if($state == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Update');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }

        return $this->renderer->json($response, $ret);

    }

}
