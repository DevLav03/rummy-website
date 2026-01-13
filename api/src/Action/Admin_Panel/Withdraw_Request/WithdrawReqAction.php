<?php

namespace App\Action\Admin_Panel\Withdraw_Request;

//Service
use App\Domain\Admin_Panel\Withdraw_Request\Service\WithdrawReqService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Exception;

final class WithdrawReqAction
{
    private WithdrawReqService $service;

    private JsonRenderer $renderer;


    public function __construct(WithdrawReqService $service, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->renderer = $jsonRenderer;
        
    }

    //Get All Data
    public function getWithdrawsReq(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $data = (array)$request->getParsedBody();
        unset($data['payload']); 
        
        $withdraws =$this->service->getWithdrawsReq($data);
        $total =$this->service->getWithdrawsReqCount($data);

        if(!empty($withdraws)){
            $ret=array("response"=>"success", "data"=>[$withdraws, ['total'=>$total]]);
        }else{
            $ret=array("response"=>"failure", "message"=>'No data found');
        }

        return $this->renderer->json($response, $ret);
    }



    //block and unblock
    public function statusWithdrawReq(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $data = (array)$request->getParsedBody(); 
        $admin_id = $data['payload']['id'];

        $server_values_array = $request->getServerParams();
        $device_type=!empty($server_values_array['HTTP_USER_AGENT'])?$server_values_array['HTTP_USER_AGENT']:'';
        if (!empty($server_values_array['HTTP_CLIENT_IP'])) {
            $ip = $server_values_array['HTTP_CLIENT_IP'];
        } elseif (!empty($server_values_array['HTTP_X_FORWARDED_FOR'])) {
            $ip = $server_values_array['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $server_values_array['REMOTE_ADDR'];
        }

    
        $valid = preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/', $ip);
        if(!$valid){
            $ip = '115.97.101.184';
        }

        $withdraw_id = (int)$args['withdraw-id'];
        $status = (int)$args['status'];

        $withdraw =$this->service->statusWithdrawReq($withdraw_id, $status, $admin_id, $ip);

        if($withdraw == 1){
            $ret=array("response"=>"success", "message"=>'Verify Successfully');
        }else if($withdraw == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Updated');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }
        
        return $this->renderer->json($response, $ret);

    }

    //Debit Test
    public function debitWithdrawReqTest(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $amount = (int)$args['in-amount'];
        $user_id = (int)$args['user-id'];

        try{
            if(isset($amount) && is_numeric($amount)){
                $withdraw_response =$this->service->checkAndSubmitWithdrawRequest($user_id, $amount);
                if($withdraw_response['status']  == 'success'){
                    $ret=array("response"=>"success", "message"=>$withdraw_response['message']);
                }else{
                    $ret=array("response"=>"failure", "message"=>$withdraw_response['message']);
                }
            }else{
                $ret=array("response"=>"failure", "message"=>'Invalid amount');
            }
        }catch(Exception $ex){
            $ret=array("response"=>"failure", "message"=>'Invalid amount');
        }
        

        // if(!empty($withdraws)){
        //     $ret=array("response"=>"success", "data"=>[$withdraws]);
        // }else{
        //     $ret=array("response"=>"failure", "message"=>'No data found');
        // }

        return $this->renderer->json($response, $ret); 
    }
    

    //Debit
    public function debitWithdrawReq(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {

        $data = (array)$request->getParsedBody();
        $user_id=$data['payload']['id'];
        unset($data['payload']); 

        try{
            if(isset($data['amount']) && is_numeric($data['amount'])){
                $amount=$data['amount'];
                $withdraw_response =$this->service->checkAndSubmitWithdrawRequest($user_id, $amount);
                if($withdraw_response['status']  == 'success'){
                    $ret=array("response"=>"success", "message"=>$withdraw_response['message']);
                }else{
                    $ret=array("response"=>"failure", "message"=>$withdraw_response['message']);
                }
            }else{
                $ret=array("response"=>"failure", "message"=>'Invalid amount');
            }
        }catch(Exception $ex){
            $ret=array("response"=>"failure", "message"=>'Invalid amount');
        }
        

        // if(!empty($withdraws)){
        //     $ret=array("response"=>"success", "data"=>[$withdraws]);
        // }else{
        //     $ret=array("response"=>"failure", "message"=>'No data found');
        // }

        return $this->renderer->json($response, $ret); 
    }

    //Webhook
    public function webhookPayoutReq(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {

        $data = (array)$request->getParsedBody();

        $webhook = $this->service->webhookPayoutReq($data);

        if($webhook['res'] == 'success'){
            $ret=array("status"=> "success", "message"=>'data received');
        }else{
            $ret=array("status"=> "Failed", "message"=>'Something Went Wrong');
        }
            
        return $this->renderer->json($response, $ret); 
    }

    //Webhook
    public function payoutStatusCheck(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {

        $data = (array)$request->getParsedBody();

        $res = $this->service->payoutStatusCheck($data);

        if($res['status_code'] == 200){
            $ret=array("response"=>"success", "message"=>$res);
        }else{
            $ret=array("response"=>"failure", "message"=>$res);
        }
            
        return $this->renderer->json($response, $ret); 
    }

}
