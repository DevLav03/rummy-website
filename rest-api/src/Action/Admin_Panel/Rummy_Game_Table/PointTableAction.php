<?php

namespace App\Action\Admin_Panel\Rummy_Game_Table;

//Service
use App\Domain\Admin_Panel\Rummy_Game_Table\Service\PointTableService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class PointTableAction
{
    private PointTableService $service;
    private JsonRenderer $renderer; 
   

    public function __construct(PointTableService $service, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }

    //Point Rummy
    public function getPointrummy(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
      
        $data = (array)$request->getParsedBody();
        unset($data['payload']);

        $point_rummy = $this->service->getPointrummy($data);

        if(!empty($point_rummy)){
            $ret=array("response"=>"success", "data"=>["point_rummy"=>$point_rummy, "total"=>$point_rummy[0]['total']]);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No data found');
        }

        return $this->renderer->json($response, $ret);

        
    }

     
    //Pool Rummy
    public function getPoolrummy(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
      
        $data = (array)$request->getParsedBody();
        unset($data['payload']);

        $pool_rummy = $this->service->getPoolrummy($data);
        

        if(!empty($pool_rummy)){
            $ret=array("response"=>"success", "data"=>["pool_rummy"=>$pool_rummy, "total"=>$pool_rummy[0]['total']]);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No Data Found');
        }

        return $this->renderer->json($response, $ret);

        
    }


    //Deal Rummy
    public function getDealrummy(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
      
        $data = (array)$request->getParsedBody();
        unset($data['payload']);

        $deal_rummy = $this->service->getDealrummy($data);
        

        if(!empty($deal_rummy)){
            $ret=array("response"=>"success", "data"=>["deal_rummy"=>$deal_rummy,"total"=>$deal_rummy[0]['total']]);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No Data Found');
        }

        return $this->renderer->json($response, $ret);

        
    }

    //Do or Die Rummy
    public function getDoDierummy(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
      
        $data = (array)$request->getParsedBody();
        unset($data['payload']);

        $dodie_rummy = $this->service->getDoDierummy($data);
        

        if(!empty($dodie_rummy)){
            $ret=array("response"=>"success", "data"=>["dodie_rummy"=>$dodie_rummy,"total"=>$dodie_rummy[0]['total']]);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No Data Found');
        }

        return $this->renderer->json($response, $ret);

        
    }

}
