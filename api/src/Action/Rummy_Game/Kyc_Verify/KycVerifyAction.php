<?php

namespace App\Action\Rummy_Game\Kyc_Verify;

//Service
use App\Domain\Rummy_Game\Kyc_Verify\Service\KycVerifyService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class KycVerifyAction
{
    private KycVerifyService $service;

    private JsonRenderer $renderer;
    

    public function __construct(KycVerifyService $service, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }

    //Get All Data
    public function getKycverifys(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $data = (array)$request->getParsedBody();
        unset($data['payload']); 
        
        $kycverifys =$this->service->getKycverifys($data);
        $total =$this->service->getKycverifysCount($data);

        if(!empty($kycverifys)){
            $ret=array("response"=>"success", "data"=>[$kycverifys,["total"=>$total]]);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No data found');
        }

        return $this->renderer->json($response, $ret);
    }


    //Get One Data
    public function getUserKycVerify(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface { 

        $data = (array)$request->getParsedBody();
        unset($data['payload']);
        $userId = (int)$args['user-id'];
     
        $kycverifys = $this->service->getUserKycVerify($data, $userId); 
      
        if(!empty($kycverifys)){        
            $ret=array("response"=>"success", "data"=>$kycverifys);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No data found');
        }

        return $this->renderer->json($response, $ret);
    }

    //upload file
    public function kycFileDownload(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        

        $kycverifyId = (int)$args['kycverify-id'];
        
        $kycverify = $this->service->getKycVerify($kycverifyId);
        
        if(!empty($kycverify)){

            $kycverifys = $kycverify[0];
            $filename = $kycverifys['pc_file'];
           
            $file_path = '../uploads/kyc-verify/'.$filename;
            $file_type = mime_content_type($file_path);
            $response = $response->withHeader('Content-Type', $file_type);
            $response = $response->withHeader('Content-Disposition', 'attachment; filename="'.$filename.'"');

            $stream = fopen($file_path, 'r+');
            $response->getBody()->write(fread($stream, (int)fstat($stream)['size']));
            // print_r($response); exit;

            return $response;
        }
        
        
    }

    //Insert
    public function insertKycVerify(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $uploadedFiles = $request->getUploadedFiles();
        unset($data['payload']); 
        
        if(array_key_exists('pc_file', $uploadedFiles)) {
            $file = $uploadedFiles['pc_file']; 
            $kycInsert = $this->service->insertKycVerify($data, $file);
        }else{
            $kycInsert = $this->service->insertKycVerify($data, null);
        } 
       
        if($kycInsert == 0){
            $ret=array("response"=>"success", "message"=>'Insert Successfully');
        }else if($kycInsert == 1){
            $ret=array("response"=>"failure", "err_message"=>'Upload File Error');
        }else if($kycInsert == 2){
            $ret=array("response"=>"failure", "err_message"=>'Upload File Size Limit 2mb');
        }else if($kycInsert == 3){
            $ret=array("response"=>"failure", "err_message"=>'Updalod File Type png,jpg,jpeg');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No Data Insert');
        }
    
        return $this->renderer->json($response, $ret);
    }


    //Change status
    public function statusKycVerify(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

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

        $kycverify_id = (int)$args['kycverify-id'];
        $verify_status = (int)$args['verify_status'];

        $kycverify =$this->service->statusKycVerify($kycverify_id, $verify_status, $admin_id, $ip);

        if($kycverify == 1){
            $ret=array("response"=>"success", "message"=>'Verify Sent Successfully');
        }else if($kycverify == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Data Updated');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }
        
        return $this->renderer->json($response, $ret);

    }

}
