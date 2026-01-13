<?php

namespace App\Action\Master_Table\Default_Mail;

//Data
use App\Domain\Master_Table\Default_Mail\Data\DefaultMailData;
use App\Domain\Master_Table\Default_Mail\Data\DefaultMailDataRead;

//Service
use App\Domain\Master_Table\Default_Mail\Service\DefaultMailService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class DefaultMailAction
{
    private DefaultMailService $service;

    private JsonRenderer $renderer;
    
    public function __construct(DefaultMailService $service, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }      
        

    //Get all mail template
    public function getAllDefaultMail(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {

        $default = $this->service->getAllDefaultMail();

        if(!empty($default)){
            $ret=array("response"=>"success", "data"=>["default_mail"=>$default]);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No data found');
        }
        
        return $this->renderer->json($response, $ret);

    }

    //Get One Mail Template
    public function getOneDefaultMail(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
       
        $data=(array)$request->getParsedBody();
        unset($data['payload']);
        $defaultmail_id = (int)$args['mail-id'];
       
        $default = $this->service->getOneDefaultMail($defaultmail_id, $data);

        if(!empty($default)){

            $ret=array("response"=>"success", "data"=>$default);

        }else{

            $ret=array("response"=>"failure", "err_message"=>'No data found');

        }
     


        return $this->renderer->json($response, $ret);    
      
    }

    //Update Data
    public function updateDefaultMail(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
       
        $data=(array)$request->getParsedBody();
        unset($data['payload']);

        $defaultmail_id = (int)$args['mail-id'];

        $default = $this->service->updateDefaultMail($defaultmail_id, $data);

        if($default == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($default == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Updated');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }         


        return $this->renderer->json($response, $ret);    
      
    }


    
    


}