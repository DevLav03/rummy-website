<?php

namespace App\Action\Master_Table\Master_Software;

//Service
use App\Domain\Master_Table\Master_Software\Service\SoftwareService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class SoftwareAction
{
    private SoftwareService $service;

    private JsonRenderer $renderer;

    public function __construct(SoftwareService $service, JsonRenderer $jsonRenderer)
    {
       
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }

    //Get Data
    public function getAndroid(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
        $android = $this->service->getAndroid();

        $ret = array("response"=>"success", "data"=>$android);
      
        return $this->renderer->json($response, $ret);

    }

    //Get Data
    public function getIos(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
        $ios = $this->service->getIos();

        $ret = array("response"=>"success", "data"=>$ios);
    
        return $this->renderer->json($response, $ret);

    }
   // $apptype = $data['app_type'];

    //Insert Data
    // public function insertSoftwareversion(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    // {

    //     $data = (array)$request->getParsedBody();
    //     unset($data['payload']);   

    //     $apptype = $data['app_type'];
    //     $version = $data['app_version'];

    //     $apptype_count = $this->service->apptypeInsertValid($apptype); 
    //     $version_count = $this->service->versionInsertValid($version); 

    //     if($apptype_count == 0){

    //         if($version_count == 0){

    //             $software = $this->service->insertSoftwareversion($data);

    //             if(!empty($software)){
    //                 $ret=array("response"=>"success", "data"=>["software-id"=>$software]);
    //             }else{
    //                 $ret=array("response"=>"failure", "err_message"=>'No Data Insert');
    //             }
    //         }
    //         else if($version_count > 0){
    //             $ret=array("response"=>"failure", "err_message"=>'This Version is already exists');
    //         }           
    //     }
    //     else if($apptype_count > 0){
    //         $ret=array("response"=>"failure", "err_message"=>'Apptype is already taken');
    //     }else{
    //         $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
    //     }
        
        
    //     return $this->renderer->json($response, $ret);
    // }
 
    public function insertSoftwareversion(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

        $data = (array)$request->getParsedBody();
        unset($data['payload']);   
      
        
        $version = $data['app_version'];
        
        $version_count = $this->service->versionInsertValid($version); 
       
        if($version_count == 0){
            $software = $this->service->insertSoftwareversion($data);

            if(!empty($software)){
                $ret=array("response"=>"success", "data"=>["software-id"=>$software]);
            }else{
                $ret=array("response"=>"failure", "err_message"=>'No Data Insert');
            }    

        }
        else if($version_count > 0){
            $ret=array("response"=>"failure", "err_message"=>'This Version is already taken');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }
        
        
        return $this->renderer->json($response, $ret);
    }
    

   

}
