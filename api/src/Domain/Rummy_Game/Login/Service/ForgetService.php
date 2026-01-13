<?php

namespace App\Domain\Rummy_Game\Login\Service;

//Service
use App\Service\Password\PasswordService;

//Repository
use App\Domain\Rummy_Game\Login\Repository\ForgetRepository;
use App\Service\Mail\MailService;


use App\Factory\LoggerFactory; 
use Psr\Log\LoggerInterface; 
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

use UAParser\Parser;

/**
 * Service
 */
final class ForgetService
{
    private ForgetRepository $repository;
    private PasswordService $passwordService;
    private MailService $mailService;
    private LoggerInterface $logger;

    public function __construct(ForgetRepository $repository, MailService $mailService, LoggerFactory $loggerFactory, PasswordService $passwordService)
    {
        $this->repository = $repository;
        $this->mailService = $mailService;
        $this->passwordService = $passwordService;
        $this->loginPasswordValidator = $loginPasswordValidator;
        $this->logger = $loggerFactory->addFileHandler('Rummy_Game/Login/Forget_Password/forget_password.log')->createLogger();
    }

   
    //Forget Email Password
    public function forgetEmailPassword(array $users, array $data, int $otp_number): int
    {    

        $result = $this->repository->forgetEmailPassword($users, $data, $otp_number);

        $sender_name = $users[0]['name'];
        $sender_mail = $users[0]['email'];
        $content = 'Hii "' .$sender_name. '", </br> 
            Your Reset Password verify OTP Number is: <b>' .$otp_number. '</b></br>';

        //Send Mail
        $mail = array($sender_mail);
        $name = array('7S Rummy'); 
        $sub='7S Rummy Reset Password!';
        $body_content= $content;
        $sample_file_attachment='';
       
        $this->mailService->sendMail($mail, $name, $sub, $body_content, $sample_file_attachment);

        $this->logger->info(sprintf('User Login with Password successfully: %s', $username));

        return $result;
    }

    public function getEmailUser(array $data): array
    {
        $result = $this->repository->getEmailUser($data);

        return $result;
    }

    
    public function forgetEmailOTPVerify(array $data): array
    {
        $result = $this->repository->forgetEmailOTPVerify($data);

        return $result;
    }

    public function updateEmailVerify(array $ip_info, int $id)
    {
        $result = $this->repository->updateEmailVerify($ip_info, $id);

        return $result;
    }

    //Forget Mobile Number Password
    public function forgetMobilePassword(array $users, array $data, int $otp_number): int
    {    

        $result = $this->repository->forgetMobilePassword($users, $data, $otp_number);
       
        //$this->mailService->sendMail($mail, $name, $sub, $body_content, $sample_file_attachment); //SMS Service

        $this->logger->info(sprintf('User Login with Password successfully: %s', $username));

        return $result;
    }

    public function getMobileUser(array $data): array
    {
        $result = $this->repository->getMobileUser($data);

        return $result;
    }

    
    public function forgetMobileOTPVerify(array $data): array
    {
        $result = $this->repository->forgetMobileOTPVerify($data);

        return $result;
    }

    public function updateMobileVerify(array $ip_info, int $id)
    {
        $result = $this->repository->updateMobileVerify($ip_info, $id);

    }

    //Reset Password
    public function resetPassword(array $data): string
    {
        // $this->changePasswordValidator->validateUpdateData($data);

        $ret = $this->resetPasswordValidator($data);

        $password = $this->passwordService->passwordEncrytion($data['new_password']);
       
        if(empty($ret)){
            $res = $this->repository->resetPassword($data, $password);
            return  "success";
        }else{
            return  (string)$ret;
        }
      
    }

    public function resetPasswordValidator(array $data): int
    {
        $new_password = $this->passwordService->passwordEncrytion($data['new_password']);
        $re_new_password = $this->passwordService->passwordEncrytion($data['re_new_password']);

        if($new_password != $re_new_password){
            return 103; //new password and rep new password are not same
        }else{
            return 0;
        }
        
    }

  
}