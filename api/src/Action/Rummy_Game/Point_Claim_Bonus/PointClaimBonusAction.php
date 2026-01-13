<?php

namespace App\Action\Rummy_Game\Point_Claim_Bonus;

//Service
use App\Domain\Rummy_Game\Point_Claim_Bonus\Service\PointClaimBonusService;


use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class PointClaimBonusAction
{
    private PointClaimBonusService $service;
    private JsonRenderer $renderer;

    public function __construct(PointClaimBonusService $service, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }


    public function PointClaimBonus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
       
        $data=(array)$request->getParsedBody();
        $user_id = $data['payload']['id'];
        unset($data['payload']);

        //print_r($user_id); exit;
        
        $claim = $this->service->PointClaimBonus($data, $user_id);
        
        if($claim[0]['res'] == 'success'){
            $ret=array("response"=>"success", "message"=>$claim[0]['msg']);
        }else if($claim[0]['res'] == 'failed'){
            $ret=array("response"=>"failure", "err_message"=>$claim[0]['msg']);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }


        return $this->renderer->json($response, $ret);    
      
    }


    
}

