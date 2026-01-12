<?php

namespace App\Domain\Admin_Panel\Login\Service;

//Data
use App\Domain\Admin_Panel\Login\Data\LoginData;
use App\Domain\Admin_Panel\Login\Data\LoginDataRead;

use App\Domain\Admin_Panel\Login\Data\LogHistoryData;
use App\Domain\Admin_Panel\Login\Data\LogHistoryDataRead;

use App\Domain\Admin_Panel\Admin\Data\AdminData;
use App\Domain\Admin_Panel\Admin\Data\AdminDataRead;

//Validator
use App\Domain\Admin_Panel\Login\Validator\LoginValidator;
use App\Domain\Admin_Panel\Login\Validator\LogHistoryValidator;

//Service
use App\Service\Auth\AuthService;

//Repository
use App\Domain\Admin_Panel\Login\Repository\LoginRepository;
use App\Domain\Admin_Panel\Admin\Repository\AdminRepository;

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
    private AdminRepository $adminRepository;

    private AuthService $authService;

    private LogHistoryValidator $logHistoryValidator;

    private LoggerInterface $logger;
  

    public function __construct(LoginRepository $repository,AdminRepository $adminRepository, AuthService $authService, LoggerFactory $loggerFactory, LoginValidator $loginValidator, LogHistoryValidator $logHistoryValidator)
    {     
        $this->loginValidator = $loginValidator;
        $this->logHistoryValidator = $logHistoryValidator;

        $this->authService = $authService;

        $this->repository = $repository;
        $this->adminRepository = $adminRepository;
    
        $this->logger = $loggerFactory->addFileHandler('Admin_Panel/Login/login.log')->createLogger();
    }

    //Login Admin
    public function adminLogin(string $username, string $password, array $ip_info):array
    {

        $data = ['username' => $username, 'password' => $password];

        $this->loginValidator->validateLoginForm($data);

        $admins = $this->repository->adminLogin($data, $ip_info);

        $this->logger->info(sprintf('Admin Login successfully: %s', $username));

        return $admins;

    }

    //Get Current Admin Users
    public function getCurrentUser(int $adminId): AdminData
    {
        $admins = $this->adminRepository->getOneAdmin($adminId);

        $result = new AdminData();

        foreach ($admins as $adminRow) {
            $admin = new AdminDataRead();
            $admin->id = $adminRow['id'];
            $admin->name = $adminRow['name'];
            $admin->username = $adminRow['username'];
            $admin->email = $adminRow['email'];
            $admin->phone_no = $adminRow['phone_no'];
            $admin->role_id = $adminRow['role_id'];
            $admin->role_name = $adminRow['role_name'];
            $admin->role_type = $adminRow['role_type'];
            $admin->active = $adminRow['active'];
            $admin->ip_restrict = $adminRow['ip_restrict'];
            $admin->created_at = $adminRow['created_at'];

            $result->admins[] = $admin;
        }

        return $result;
    }

   
    //Logout Admin
    public function adminLogout($data): int
    {
        $admin = $this->repository->adminLogout($data);

        $this->logger->info(sprintf('Admin logout successfully: %s', $admin_id));

        return $admin;
    }

    //Admin Log History 
    public function logAdminHistory(array $data, int $admin_id): LogHistoryData
    {
        $this->logHistoryValidator->validateGetData($data);
        $admins = $this->repository->logAdminHistory($data, $admin_id);
        $admin_mod = $this->parseUserAgent($admins);

        $result = new LogHistoryData();

        foreach ($admins as $adminRow) {
            $admin = new LogHistoryDataRead();
            $admin->admin_id = $adminRow['admin_id'];
            $admin->login_device = $adminRow['login_device'];
            $admin->action = $adminRow['action'];
            $admin->location_ip = $adminRow['location_ip'];
            $admin->created_at = $adminRow['created_at'];

            $result->admin_log[] = $admin;
        }

        return  $result;   
    
    }

    public function parseUserAgent(& $fields){
        $i=0;
        $parser = Parser::create();
        foreach($fields as $field){
            if($field['login_device']){
                $result = $parser->parse("Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Safari/537.36");
                $fields[$i]['login_device'] =  $result->toString();   
            }
            $i++;
        }
        return $fields;
    }

    public function logAdminHistoryCount( array $data, int $admin_id): int
    {
        $total = $this->repository->logAdminHistoryCount($data, $admin_id);
        return  $total ; 
    }

    //Admin Time-in
    public function timeinAdmin( int $admin_id): int
    {
        $admin = $this->repository->timeinAdmin( $admin_id);

        $this->logger->info(sprintf('Admin Time-in successfully: %s', $admin_id));

        return $admin;
    }

    //Admin Time-in
    public function timeoutAdmin(int $admin_id): int
    {
        //print_r($data); exit;
        $admin = $this->repository->timeoutAdmin($admin_id );

        $this->logger->info(sprintf('Admin Time-out successfully: %s', $admin_id));

        return $admin;
    }
}