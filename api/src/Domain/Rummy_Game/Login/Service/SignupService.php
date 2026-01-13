<?php

namespace App\Domain\Rummy_Game\Login\Service;

//Data
use App\Domain\Rummy_Game\Login\Data\LoginData;
use App\Domain\Rummy_Game\Login\Data\LoginDataRead;


//Validator
use App\Domain\Rummy_Game\Login\Validator\SignupValidator;

//Service
use App\Service\CommonService;


//Repository
use App\Domain\Rummy_Game\Login\Repository\SignupRepository;

use App\Factory\LoggerFactory; 
use Psr\Log\LoggerInterface; 
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

use UAParser\Parser;

/**
 * Service
 */
final class SignupService
{
       
    private SignupValidator $signupValidator;
    private CommonService $commonService;
    private SignupRepository $repository;

    private LoggerInterface $logger;

    public function __construct(SignupRepository $repository, LoggerFactory $loggerFactory, SignupValidator $signupValidator, CommonService $commonService)
    {
        $this->repository = $repository;
        $this->signupValidator = $signupValidator;
        $this->commonService = $commonService;

        $this->logger = $loggerFactory->addFileHandler('Rummy_Game/Login/login_with_password.log')->createLogger();
    }

   
    public function usersRegsiter(array $data): int
    {

        $this->signupValidator->validateData($data);

        $loginData = $this->repository->usersRegsiter($data);

        return $loginData;

    }

    
    public function stateCount(string $state):int
    {

        $state = $this->repository->stateCount($state);
        
        return $state;

    }

    public function insertOTPcode(string $phone_no,int $otp_number):int
    {

        $loginData = $this->repository->insertOTPcode($phone_no, $otp_number);

        return $loginData;

    }


    public function usersRegsiterVerify(array $data, array $ip_info, array $ip_details): array
    {

        $user_ref_code = $this->commonService->getRefCode();
        //print_r($user_ref_code); exit;

        $loginData = $this->repository->usersRegsiterVerify($data, $ip_info, $ip_details, $user_ref_code);

        $this->logger->info(sprintf('User Regsiter successfully: %s', $data));

        return $loginData;

    }

}