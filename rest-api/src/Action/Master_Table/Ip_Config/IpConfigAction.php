<?php

namespace App\Action\Master_Table\Ip_Config;

//Service
use App\Domain\Master_Table\Ip_Config\Service\IpConfigService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class IpConfigAction
{
    private IpConfigService $service;
    

    private JsonRenderer $renderer;

    public function __construct(IpConfigService $service, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }

    //Get All Data
    public function getIpConfig(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
       
        $ip = $this->service->getIpConfig();

        $ret=array("response"=>"success", "data"=>$ip);
        
        return $this->renderer->json($response, $ret);

    }

    //update data
    public function updateIpConfig(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
       
        $data=(array)$request->getParsedBody();
        unset($data['payload']); 

        $ip = $this->service->updateIpConfig($data); 

        if($ip == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($ip == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Updated');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }
    
        return $this->renderer->json($response, $ret);    
      
    }

 

}
