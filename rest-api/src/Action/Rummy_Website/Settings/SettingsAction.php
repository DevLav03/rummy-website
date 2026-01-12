<?php

namespace App\Action\Rummy_Website\Settings;

//Service
use App\Domain\Rummy_Website\Settings\Service\SettingsService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class SettingsAction
{
    private SettingsService $service;
    private JsonRenderer $renderer;

    public function __construct(SettingsService $service, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }

    //Get All Data
    public function getLogoSettings(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
       
        $settings = $this->service->getLogoSettings();

        $ret=array("response"=>"success", "data"=>$settings);
        
        return $this->renderer->json($response, $ret);

    }

   
    
    //Update Data
    public function updateLogoSettings(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

       

        $data = (array)$request->getParsedBody();
        $uploadedFiles = $request->getUploadedFiles();

        //var_dump($uploadedFiles); exit;

        $logo_img = $uploadedFiles['logo_image'];
        $img = $logo_img->getClientFilename(); 

        if(!empty($img)){
            $imageInsert = $this->service->updateLogoSettings($data, $logo_img);

        } else{
            $imageInsert = $this->service->updateLogoSettings($data, null);
        } 
    

        // if(array_key_exists('logo_image', $uploadedFiles)) {
        //     $logo_img = $uploadedFiles['logo_image']; 
        //     $imageInsert = $this->service->updateLogoSettings($data, $logo_img);
        // }
       

        //print_r($imageInsert); exit;
    
        if($imageInsert == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($imageInsert == 31){
            $ret=array("response"=>"failure", "err_message"=>'Logo image upload File Error');
        }else if($imageInsert == 21){
            $ret=array("response"=>"failure", "err_message"=>'Logo image File Size Limit 2mb');
        }else if($imageInsert == 11){
            $ret=array("response"=>"failure", "err_message"=>'Logo image File Type png,jpg,jpeg'); 
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No Data Update');
        }
    
        return $this->renderer->json($response, $ret);
    }

    

    

}
