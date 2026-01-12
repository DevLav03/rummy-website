<?php

namespace App\Action\Master_Table\Master_Game_Country;

//Service
use App\Domain\Master_Table\Master_Game_Country\Service\CountryService;


use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class CountryAction
{
    private CountryService $service;
    

    private JsonRenderer $renderer;

    public function __construct(CountryService $service, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }

    //Get All Data
    public function getCountry(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
       
        $country = $this->service->getCountry();

        $ret=array("response"=>"success", "data"=>$country);
        
        return $this->renderer->json($response, $ret);

    }

    //Get One Data
    public function getOneCountry(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface { 

        $countryId = (int)$args['country-id'];
        
        $country = $this->service->getOneCountry($countryId); 

        if(!empty($country)){

            $ret=array("response"=>"success", "data"=>$country);

        }else{

            $ret=array("response"=>"failure", "err_message"=>'No data found');

        }

        return $this->renderer->json($response, $ret);
    } 

    //Insert Data
    public function insertCountry(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

        $data = (array)$request->getParsedBody();
        unset($data['payload']);   


        $country = $this->service->insertCountry($data);

        if(!empty($country)){
            $ret=array("response"=>"success", "data"=>["country-id"=>$country]);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No Data Insert');
        }
        
        return $this->renderer->json($response, $ret);
    }

    //Update Data
    public function updateCountry(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
       
        $data=(array)$request->getParsedBody();
        unset($data['payload']);

        $country_id = (int)$args['country-id'];
        
        $country = $this->service->updateCountry($country_id, $data);

        if($country == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($country == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Updated');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }
 
        return $this->renderer->json($response, $ret);    
      
    }


    //Delete Data
    public function deleteCountry(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $country_Id = (int)$args['country-id'];
        $country = $this->service->deleteCountry($country_Id);

        if($country == 1){
            $ret=array("response"=>"success", "message"=>'Delete Successfully');
        }else if($country == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Delete');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }

        return $this->renderer->json($response, $ret);

    }

}
