<?php

namespace App\Action\Rummy_Website\SocialMedia;

//Service
use App\Domain\Rummy_Website\SocialMedia\Service\SocialMediaService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class SocialMediaAction
{
    private SocialMediaService $service;
    private JsonRenderer $renderer;

    public function __construct(JsonRenderer $jsonRenderer, SocialMediaService $service)
    {
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }

    //Get All Data
    public function getSocialMedia(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
        

        $socialmedia = $this->service->getSocialMedia();

        $ret=array("response"=>"success", "data"=>$socialmedia);
        
        return $this->renderer->json($response, $ret);

    }

    //Update Data
    public function updateSocialMedia(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
       
        $data=(array)$request->getParsedBody();
        unset($data['payload']);        

        $socialmedia = $this->service->updateSocialMedia( $data);

        if($socialmedia == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($socialmedia == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Updated');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }    
    
        return $this->renderer->json($response, $ret);    
      
    }

}
