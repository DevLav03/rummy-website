<?php

namespace App\Action\Master_Table\Master_Game_Table;

//Data
use App\Domain\Master_Table\Master_Game_Table\Data\GameTableData;
use App\Domain\Master_Table\Master_Game_Table\Data\GameTableDataRead;

//Service
use App\Domain\Master_Table\Master_Game_Table\Service\GameTableService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class GameTableAction
{
    private GameTableService $service;

    private JsonRenderer $renderer;
    
    public function __construct(GameTableService $service, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }

    //Get Data
    public function getGameTable(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {

        $data = (array)$request->getParsedBody();
        unset($data['payload']);
       
        $gametable = $this->service->getGameTable($data);

        if(!empty($gametable)){
            $ret=array("response"=>"success", "data"=>["game_tables"=>$gametable, "total"=>$gametable[0]['total']]);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No data found');
        }
        
        return $this->renderer->json($response, $ret);

    }   
    
    //Insert Data
    public function insertGameTable(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

        $data = (array)$request->getParsedBody();
        unset($data['payload']);   
        

        $match_count = $this->service->matchInsertValid($data); 
     
        if($match_count == 0){
            
                $gametable = $this->service->insertGameTable($data);
                if(!empty($gametable)){
                    $ret=array("response"=>"success", "data"=>["gametable_id"=>$gametable]);
                }else{
                    $ret=array("response"=>"failure", "err_message"=>'No Data Insert');
                }
            
        }else if($match_count > 0){
            $ret=array("response"=>"failure", "err_message"=>'Table is already exists');
        } 
        

        
        return $this->renderer->json($response, $ret);
    }    
        
        
    //Update Data
    public function updateGameTable(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
       
        $data=(array)$request->getParsedBody();
        unset($data['payload']);

        $gametable_id = (int)$args['gametable-id'];

        $match_count = $this->service->matchUpdateValid($data, $gametable_id);
        
        if($match_count == 0){

            $gametable = $this->service->updateGameTable($gametable_id, $data);

            if($gametable == 1){
                $ret=array("response"=>"success", "message"=>'Update Successfully');
            }else if($gametable == 0){
                $ret=array("response"=>"failure", "err_message"=>'No Record Updated');
            }else{
                $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
            }          
        }
        else if($match_count > 0){
            $ret=array("response"=>"failure", "err_message"=>'GameTable is Already Exists.');
        } 

        return $this->renderer->json($response, $ret);    
      
    }

    //Status Change
    public function gametableStatus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {


        $gametable_Id = (int)$args['gametable-id'];

        $status = (int)$args['status'];

        $gametable =$this->service->gametableStatus($gametable_Id, $status);

        if($gametable == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($gametable == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Update');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }

        return $this->renderer->json($response, $ret);

    }


    //Delete Data
    public function deleteGameTable(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $gametable_Id = (int)$args['gametable-id'];
        $gametable = $this->service->deleteGameTable($gametable_Id);

        if($gametable == 1){
            $ret=array("response"=>"success", "message"=>'Delete Successfully');
        }else if($gametable == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Delete');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }

        return $this->renderer->json($response, $ret);

    }

}
