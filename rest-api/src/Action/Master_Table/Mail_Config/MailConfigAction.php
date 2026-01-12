<?php

namespace App\Action\Master_Table\Mail_Config;

//Data
use App\Domain\Master_Table\Mail_Config\Data\MailConfigData;
use App\Domain\Master_Table\Mail_Config\Data\MailConfigDataRead;

//Service
use App\Domain\Master_Table\Mail_Config\Service\MailConfigService;

//Mail Service
use App\Service\Mail\MailService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class MailConfigAction
{
    private MailConfigService $service;
    

    private JsonRenderer $renderer;

    public function __construct(MailConfigService $service, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }
    // public function __construct(array $emailSettings = [])
    // {

    //     $this->host = (string)($emailSettings['host'] ?? '');
    //     $this->port = (int)($emailSettings['port'] ?? 25);
    //     $this->username = (string)($emailSettings['username'] ?? '');
    //     $this->password = (string)($emailSettings['password'] ?? '');
    //     $this->from_name = (string)($emailSettings['from_name'] ?? '');
    //     $this->from_email = (string)($emailSettings['from_email'] ?? '');
    //     $this->smtp_auth = (bool)($emailSettings['smtp_auth'] ?? false);
    //     $this->debug = (int)($emailSettings['debug'] ?? 0);
    // }


    //Get All Data
    public function getMailConfig(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
       
        $mail = $this->service->getMailConfig();

        //print_r($mail); exit;

        $result = new MailConfigData();
     
        foreach ($mail as $mailRow) {
            $mail = array();
            $mail['sender_mail'] = $mailRow['sender_mail'];
            $mail['from_name'] = $mailRow['from_name'];
            $mail['smtp_host'] = $mailRow['smtp_host'];
            $mail['smtp_type']  = $mailRow['smtp_type'];
            $mail['smtp_port']  = $mailRow['smtp_port'];
            $mail['smtp_username']  = $mailRow['smtp_username'];
            $mail['smtp_password']  = $mailRow['smtp_password'];
            $mail['smtp_auth']  = $mailRow['smtp_auth'];


            $result->mail_config[] = $mail;
        }

        $ret=array("response"=>"success", "data"=>$mail);
        
        return $this->renderer->json($response, $ret);

    }

   

    //update data
    public function updateMailConfig(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
       
        $data=(array)$request->getParsedBody();
        unset($data['payload']); 

        $mail = $this->service->updateMailConfig($data); 

        if($mail == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($mail == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Updated');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }
    
        return $this->renderer->json($response, $ret);    
      
    }

 

}
