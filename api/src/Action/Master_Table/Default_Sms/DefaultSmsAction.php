<?php

namespace App\Action\Master_Table\Default_Sms;

//Data
use App\Domain\Master_Table\Default_Sms\Data\DefaultSmsData;
use App\Domain\Master_Table\Default_Sms\Data\DefaultSmsDataRead;

//Service
use App\Domain\Master_Table\Default_Sms\Service\DefaultSmsService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class DefaultSmsAction
{
    private DefaultSmsService $service;

    private JsonRenderer $renderer;
    
    public function __construct(DefaultSmsService $service, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }      
        

    //Get all sms template
    public function getAllDefaultSms(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {

        $default = $this->service->getAllDefaultSms();

        if(!empty($default)){
            $ret=array("response"=>"success", "data"=>["default_sms"=>$default]);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No data found');
        }
        
        return $this->renderer->json($response, $ret);

    }

    //Get One SMS Template
    public function getOneDefaultSms(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
       
        $data=(array)$request->getParsedBody();
        unset($data['payload']);
        $defaultsms_id = (int)$args['sms-id'];
       
        $default = $this->service->getOneDefaultSms($defaultsms_id, $data);

        if(!empty($default)){

            $ret=array("response"=>"success", "data"=>$default);

        }else{

            $ret=array("response"=>"failure", "err_message"=>'No data found');

        }
     


        return $this->renderer->json($response, $ret);    
      
    }

    //Update Data
    public function updateDefaultSms(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
       
        $data=(array)$request->getParsedBody();
        unset($data['payload']);

        $defaultsms_id = (int)$args['sms-id'];

        $default = $this->service->updateDefaultSms($defaultsms_id, $data);

        if($default == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($default == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Updated');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }         


        return $this->renderer->json($response, $ret);    
      
    }


    
    


}
