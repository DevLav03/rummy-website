<?php

namespace App\Action\Master_Table\Sms_Config;

//Service
use App\Domain\Master_Table\Sms_Config\Service\SmsConfigService;


use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class SmsConfigAction
{
    private SmsConfigService $service;
    


    private JsonRenderer $renderer;

    public function __construct(SmsConfigService $service, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }

    //Get All Data
    public function getSmsConfig(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
       
        $sms = $this->service->getSmsConfig();

        $ret=array("response"=>"success", "data"=>$sms);
        
        return $this->renderer->json($response, $ret);

    }

    //update data
    public function updateSmsConfig(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
       
        $data=(array)$request->getParsedBody();
        unset($data['payload']); 

    
        $sms = $this->service->updateSmsConfig($data); 

        if($sms == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($sms == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Updated');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }
    
        return $this->renderer->json($response, $ret);    
      
    }

 

}
