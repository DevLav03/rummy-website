<?php

namespace App\Domain\Rummy_Game\Login\Service;

//Repository
use App\Service\CommonService;
use App\Domain\Rummy_Game\Login\Repository\GoogleLoginRepository;

use App\Factory\LoggerFactory; 
use Psr\Log\LoggerInterface; 
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * Service
 */
final class GoogleLoginService
{
    private GoogleLoginRepository $repository;
    private CommonService $commonService;
    private LoggerInterface $logger;

    public function __construct(GoogleLoginRepository $repository, CommonService $commonService, LoggerFactory $loggerFactory)
    {
        $this->repository = $repository;
        $this->commonService = $commonService;
        $this->logger = $loggerFactory->addFileHandler('Admin_Panel/Login/Google_Login/login_with_google.log')->createLogger();
    }

    public function googleLogin(array $data, array $ip_info, array $ip_details): array
    {
        $user_ref_code = $this->commonService->getRefCode();

        $loginData = $this->repository->googleLogin($data, $ip_info, $ip_details, $user_ref_code);

        $this->logger->info(sprintf('User Login with Google successfully: %s', $data['email']));

        return $loginData;
    }

    public function userLogout(int $userId, array $ip_info, array $ip_details): int
    {

        $loginData = $this->repository->userLogout($userId, $ip_info, $ip_details);

        $this->logger->info(sprintf('User Logout successfully: %s', $userId));

        return $loginData;
    }



}