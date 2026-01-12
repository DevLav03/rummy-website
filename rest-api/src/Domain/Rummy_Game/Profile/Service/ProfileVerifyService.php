<?php

namespace App\Domain\Rummy_Game\Profile\Service;

//Service
use App\Service\Mail\MailService;
use App\Service\SMS\SmsService;
use App\Domain\Master_Table\Default_Mail\Service\DefaultMailService;

//Repository
use App\Domain\Rummy_Game\Profile\Repository\ProfileVerifyRepository;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;

final class ProfileVerifyService
{
    private ProfileVerifyRepository $repository;
    private DefaultMailService $service;
    private MailService $mailService;
    private SmsService $smsService;
    private LoggerInterface $logger;

    public function __construct(ProfileVerifyRepository $repository, DefaultMailService $service, LoggerFactory $loggerFactory, MailService $mailService, SmsService $smsService)
    {
        $this->repository = $repository;
        $this->service = $service;
        $this->smsService = $smsService;
        $this->mailService = $mailService;
    
        $this->logger = $loggerFactory->addFileHandler('Rummy_Game/Profile/Verify/User-Profile-verify.log')->createLogger();   
    }

    //Mobile Verify
    public function mobileSendOTP(string $mobile_num, int $userId, int $otp_number): int
    {
        $result = $this->repository->mobileSendOTP($mobile_num, $userId, $otp_number);

        //$this->smsService->sendOTP(); //SMS service

        return  $result ;   
    
    }  

    public function mobileCount(string $mobile_num, int $userId): array
    {
        $result = $this->repository->mobileCount($mobile_num, $userId);

        return  $result ;   
    
    } 
    
    public function mobileOTPVerify(array $data, int $userId, array $ip_info): array
    {
        $result = $this->repository->mobileOTPVerify($data, $userId, $ip_info);

        return  $result ;   
    
    } 

    //Email Verify
    public function emailSendOTP(array $data, int $userId, int $otp_number): int
    {
        $result = $this->repository->emailSendOTP($data, $userId, $otp_number);
        // print_r($otp_number); exit;
        $user_details = $this->repository->getUser($userId);
        
        $mail_details = $this->service->getMailTemplate('user_mail_verify');

        //Default Mail Variable
        $name = $user_details[0]['name'];
        $code = $otp_number;
        $company_name = $mail_details['name'];
        // print_r($company_name); exit;

        //Send Mail
        $mail = array($data['email']);
        $sender_name = array($name); 
        $sub= $mail_details['subject'];
       
 
        $body_content =  $this->named_printf($mail_details['message'], array('name'=>$name,'company_name'=>$company_name, 'code'=>$code)); 
       
        $sample_file_attachment='';
 
        $this->mailService->sendMail($mail, $sender_name, $sub, $body_content, $sample_file_attachment);

        return  $result ;   
    
    }  
    function named_printf ($format_string, $values) {
        extract($values);
        $result = $format_string;
        eval('$result = "'.$format_string.'";');
        return $result;
    }

    public function emailCount(array $data, int $userId): array
    {
        $result = $this->repository->emailCount($data, $userId);

        return  $result ;   
    
    } 
    
    public function emailOTPVerify(array $data, int $userId, array $ip_info): array
    {
        $result = $this->repository->emailOTPVerify($data, $userId, $ip_info);

        return  $result ;   
    
    } 
}
