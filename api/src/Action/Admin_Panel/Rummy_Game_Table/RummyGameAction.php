<?php

namespace App\Action\Admin_Panel\Rummy_Game_Table;

//Service
use App\Domain\Admin_Panel\Rummy_Game_Table\Service\RummyTableService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class RummyGameAction
{
    private RummytableService $service;
    private JsonRenderer $renderer;

    public function __construct(RummytableService $service, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }



    //Insert Data
    public function insertRummytable(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        unset($data['payload']);

        $rummytable = $this->service->insertRummytable($data);

        if(!empty($rummytable)){
            $ret=array("response"=>"success", "data"=>["rummytable_id"=>$rummytable]);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No Data Insert');
        }
  
        return $this->renderer->json($response, $ret);
    }

    //change status
    public function changeStatus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $id = (int)$args['id'];
        $status = (int)$args['status'];

        $rummytable =$this->service->changeStatus($id,$status);

        if($rummytable == 1){
            $ret=array("response"=>"failure", "message"=>'Updated Successfully');
        }else if($rummytable == 0){
            $ret=array("response"=>"failure", "message"=>'No Record Updated');
        }else{
            $ret=array("response"=>"failure", "message"=>' Not Unblock');
        }
        
        return $this->renderer->json($response, $ret);

    }

    //Delete status change
    public function deleteTable(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $id = (int)$args['id'];

        $rummytable =$this->service->deleteTable($id);

        if($rummytable == 1){
            $ret=array("response"=>"failure", "message"=>'Updated Successfully');
        }else if($rummytable == 0){
            $ret=array("response"=>"failure", "message"=>'No Record Updated');
        }else{
            $ret=array("response"=>"failure", "message"=>' Not Unblock');
        }
        

        return $this->renderer->json($response, $ret);

    }


   

    

}
