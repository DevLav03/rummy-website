<?php

namespace App\Domain\Rummy_Game\Login\Service;

//Data
use App\Domain\Rummy_Game\Login\Data\LoginData;
use App\Domain\Rummy_Game\Login\Data\LoginDataRead;
use App\Domain\Rummy_Game\Login\Data\LastLoginData;
use App\Domain\Rummy_Game\Login\Data\LastLoginDataRead;


//Validator
use App\Domain\Rummy_Game\Login\Validator\LoginPasswordValidator;

//Repository
use App\Domain\Rummy_Game\Login\Repository\LoginRepository;

use App\Factory\LoggerFactory; 
use Psr\Log\LoggerInterface; 
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

use UAParser\Parser;

/**
 * Service
 */
final class LoginService
{
    private LoginRepository $repository;
    private LoggerInterface $logger;
    private LoginPasswordValidator $loginPasswordValidator;

    public function __construct(LoginRepository $repository, LoggerFactory $loggerFactory,LoginPasswordValidator $loginPasswordValidator)
    {
        $this->repository = $repository;
        $this->loginPasswordValidator = $loginPasswordValidator;
        $this->logger = $loggerFactory->addFileHandler('Rummy_Game/Login/login.log')->createLogger();
    }

   
    //Login With Password
    public function loginPassword(string $username, string $password, array $ip_info, array $ip_details): array
    {


        $data = ["mobile_or_mail" => $username, "password" => $password];

        $this->loginPasswordValidator->validateData($data); 

        $loginData = $this->repository->loginPassword($data, $ip_info, $ip_details);

        $this->logger->info(sprintf('User Login with Password successfully: %s', $username));

        //print_r($loginData); exit;

        return $loginData;
    }

    
    //OTP Login
    public function otpLogin(array $data): int
    {

        $loginData = $this->repository->otpLogin($data);

        return  $loginData ;   
    
    }

    
    public function stateCount(string $state):int
    {

        $state = $this->repository->stateCount($state);
        
        return $state;

    }


    //Insert OTP
    public function insertOTPcode(string $phone_no, int $otp_number): int
    {

        $loginData = $this->repository->insertOTPcode($phone_no, $otp_number);

        return  $loginData ;   
    
    }

    //OTP Login Verify
    public function otpLoginVerify(array $data, array $ip_info, array $ip_details): array
    {
        $loginData = $this->repository->otpLoginVerify($data, $ip_info, $ip_details);  
        
        $this->logger->info(sprintf('User Login with OTP successfully: %s', $data['phone_no']));
       
        return $loginData;
    }

    //Last Login
    public function LastLogin(): LastLoginData
    {
        $logindetails = $this->repository->LastLogin();

        $result = new LastLoginData();

        foreach ($logindetails as $logindetailsRow) {
            $logindetails = new LastLoginDataRead();
            $logindetails->id = $logindetailsRow['id'];
            $logindetails->user_id = $logindetailsRow['user_id'];
            $logindetails->login_device = $logindetailsRow['login_device'];
            $logindetails->country_name = $logindetailsRow['country_name'];
            $logindetails->state_name = $logindetailsRow['state_name'];
            $logindetails->city_name = $logindetailsRow['city_name'];
            $logindetails->action = $logindetailsRow['action'];
            $logindetails->location_ip = $logindetailsRow['location_ip'];
            $logindetails->created_at = $logindetailsRow['created_at'];

            $result->logindetails[] = $logindetails;
        }

        return $result;
       
    }
}