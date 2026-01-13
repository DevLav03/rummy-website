<?php

namespace App\Service\SMS;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;


final class SmsService
{

    private LoggerInterface $logger; 

    public function __construct(LoggerFactory $loggerFactory)
    {
        $this->logger = $loggerFactory->addFileHandler('Common/Service/SMS/sms_service.log')->createLogger();  
    }

    public function SendOTPcode($mobile, $otp){

        //This is a DEMO SMS. 
        $url = 'https://www.fast2sms.com/dev/bulkV2?authorization=123&variables_values='.$otp.'&route=otp&numbers='.$mobile;
         
        $crl = curl_init();
        curl_setopt($crl, CURLOPT_HEADER, 0);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($crl, CURLOPT_URL, $url);
        curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($crl);
        if(!$response){
           die('Error: "' . curl_error($crl) . '" - Code: ' . curl_errno($crl));
        }
         
        curl_close($crl);
        return $response;
    }


  
}
