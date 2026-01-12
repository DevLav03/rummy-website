<?php

namespace App\Action\Master_Table\Social_Config;

//Service
use App\Domain\Master_Table\Social_Config\Service\SocialConfigService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class SocialConfigAction
{
    private SocialConfigService $service;
    

    private JsonRenderer $renderer;

    public function __construct(SocialConfigService $service, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }

    //Get All Data
    public function getSocialConfig(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
       
        $social = $this->service->getSocialConfig();

        $ret=array("response"=>"success", "data"=>$social);
        
        return $this->renderer->json($response, $ret);

    }

    //update data
    public function updateSocialConfig(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
       
        $data=(array)$request->getParsedBody();
        unset($data['payload']); 

        $social = $this->service->updateSocialConfig($data); 

        if($social == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($social == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Updated');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }
    
        return $this->renderer->json($response, $ret);    
      
    }

 

}
