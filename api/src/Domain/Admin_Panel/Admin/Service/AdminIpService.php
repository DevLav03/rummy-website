<?php

namespace App\Domain\Admin_Panel\Admin\Service;

//Data
use App\Domain\Admin_Panel\Admin\Data\AdminIpData;
use App\Domain\Admin_Panel\Admin\Data\AdminIpDataRead;

//Validator
use App\Domain\Admin_Panel\Admin\Validator\CreateIPValidator;
use App\Domain\Admin_Panel\Admin\Validator\UpdateIPValidator;

//Repository
use App\Domain\Admin_Panel\Admin\Repository\AdminIpRepository;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;


final class AdminIpService
{
    private AdminIpRepository $repository;

    private CreateIPValidator $createValidator;
    private UpdateIPValidator $updateValidator;

    private LoggerInterface $logger;

   

    public function __construct(AdminIpRepository $repository, LoggerFactory $loggerFactory, CreateIPValidator $createValidator, UpdateIPValidator $updateValidator)
    {
        $this->repository = $repository;

        $this->createValidator = $createValidator;
        $this->updateValidator = $updateValidator;

        $this->logger = $loggerFactory->addFileHandler('Admin_Panel/Admin/Admin_IP.log')->createLogger();
    }
    
    //Remove Admin IP Restrict Status
    public function adminIpStatus(int $adminId, int $status): int
    {
        $admin = $this->repository->adminIpStatusById($adminId, $status);

        return  $admin; 
    }

    //Get Admin IP
    public function getOneAdminIp(int $adminId): AdminIpData
    {
        $admins = $this->repository->getOneAdminIp($adminId);

        $result = new AdminIpData();

        foreach ($admins as $adminRow) {
            $admin = new AdminIpDataRead();
            $admin->id = $adminRow['id'];
            $admin->admin_id = $adminRow['admin_id'];
            $admin->ip_address = $adminRow['ip_address'];

            $result->admin_ip[] = $admin;
        }

        return $result;

    }

    //Insert Admin IP Address
    public function insertAdminIp(array $data): int
    {

        $this->createValidator->validateCreateData($data);

        $admin = $this->repository->insertAdminIp($data);
      
        return  $admin;  
    }

    public function checkInsertAdminIp(array $data): int
    {

        $admin = $this->repository->checkInsertAdminIp($data);
      
        return  $admin;  
    }

    //Update Admin IP Address
    public function updateAdminIp(int $id, array $data): int
    {
        $this->updateValidator->validateUpdateData($data);

        $admin = $this->repository->updateAdminIpById($id, $data);

        return  $admin ;
    }

    public function checkUpdateAdminIp(int $id, array $data): int
    {

        $admin = $this->repository->checkUpdateAdminIp($id, $data);
      
        return  $admin;  
    }

    //Admin IP Status Change
    public function updateAdminIpStatus(int $adminId)
    {
        $ip_status = $this->repository->updateAdminIpStatus($adminId);   
    }

    //Delete Admin IP Address
    public function deleteAdminIp(int $id): int
    {
        $admin = $this->repository->deleteAdminIpById($id);
        
        return  $admin ;  
    }

   
}
