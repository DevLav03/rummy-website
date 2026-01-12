<?php

namespace App\Action\Rummy_Game\User_Bank_Details;

//Service
use App\Domain\Rummy_Game\User_Bank_Details\Service\BankdetailsService;


use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

//Spreadsheet
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


final class BankdetailsAction
{
    private BankdetailsService $service;
    private JsonRenderer $renderer;

    public function __construct(BankdetailsService $service, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }

    //Get All Data
    public function getBankDetails(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
       
        $bankdetails = $this->service->getBankDetails();

        if(!empty($bankdetails)){
            $ret=array("response"=>"success", "data"=>[$bankdetails]);
        }else{
            $ret=array("response"=>"failure", "message"=>'No data found');
        }
        
        return $this->renderer->json($response, $ret);

    }

    //bank details spreadsheet
    public function getAllBankDetails(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
       
        $excel = new Spreadsheet();
        $sheet = $excel->setActiveSheetIndex(0);

        $bankdetails = $this->service->getBankDetails();
        $arr = (array) $bankdetails;

        $row_count= count($arr['bankdetails']);
        $excel->getActiveSheet()->getStyle('A1:H1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('00FF7F');
        $excel->getActiveSheet()->setAutoFilter('A1:H5');

        $final_excel_array=array();
        foreach($arr_bankdetails  as $key => $val){
            array_push($individual_array,$val);
        }
        for($i=0;$i<$row_count;$i++){
            $individual_array=array();
            $arr_bankdetails=(array)$arr['bankdetails'][$i];
            // print_r($arr_bankdetails); exit;
            if($i==0){
                foreach($arr_bankdetails  as $key => $val){
                    array_push($individual_array,strtoupper($key));
                }
                array_push($final_excel_array,$individual_array);
            }
            // print_r( $arr_bankdetails);exit;
            $individual_array=array();
            foreach($arr_bankdetails  as $key => $val){
                array_push($individual_array, $val);
            }
            array_push($final_excel_array,$individual_array);
           
        }
        
        $len=$row_count+1;
        $cell_num='A'.$len;

        $sheet->fromArray($final_excel_array, NULL, 'A1'); 
        $excelWriter = new Xlsx($excel);


         // We have to create a real temp file here because the
        // save() method doesn't support in-memory streams.
        $tempFile = tempnam(File::sysGetTempDir(), 'phpxltmp');
        $tempFile = $tempFile ?: __DIR__ . '/temp.xlsx';
        $excelWriter->save($tempFile);

        // For Excel2007 and above .xlsx files   
        $response = $response->withHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response = $response->withHeader('Content-Disposition', 'attachment; filename="file.xlsx"');

        $stream = fopen($tempFile, 'r+');
        $response->getBody()->write(fread($stream, (int)fstat($stream)['size']));
        return $response;

    }

    //Get One Data
    public function getUserBankDetails(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface { 

        $data = (array)$request->getParsedBody();
        unset($data['payload']);
        $userId = (int)$args['user-id'];
     
        $bankdetails = $this->service->getUserBankDetails($data, $userId); 
      
        if(!empty($bankdetails)){        
            $ret=array("response"=>"success", "data"=>$bankdetails);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No data found');
        }

        return $this->renderer->json($response, $ret);
    }
    
    //Insert Data
    public function insertBankDetails(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

        $data = (array)$request->getParsedBody();
        unset($data['payload']);   
        
        $bankdetails = $this->service->insertBankDetails($data);

        if(!empty($bankdetails)){
            $ret=array("response"=>"success", "data"=>["bankdetails-id"=>$bankdetails]);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No Data Insert');
        }

        
        return $this->renderer->json($response, $ret);
    }

    //Update Data
    public function updateBankDetails(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
       
        $data=(array)$request->getParsedBody();
        unset($data['payload']);

        $bankdetails_id = (int)$args['bankdetails-id'];
        
        $bankdetails = $this->service->updateBankDetails($bankdetails_id, $data);

        if($bankdetails == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($bankdetails == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Updated');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }        
    
        return $this->renderer->json($response, $ret);    
      
    }

 

    

}
