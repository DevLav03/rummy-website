<?php

namespace App\Domain\Admin_Panel\Admin\Service;

//Data
use App\Domain\Admin_Panel\Admin\Data\AdminData;
use App\Domain\Admin_Panel\Admin\Data\AdminDataRead;

//Validator
use App\Domain\Admin_Panel\Admin\Validator\AdminCreateValidator;
use App\Domain\Admin_Panel\Admin\Validator\AdminUpdateValidator;

//Repository
use App\Domain\Admin_Panel\Admin\Repository\AdminRepository;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;


final class AdminService
{
    private AdminRepository $repository;
    private AdminCreateValidator $createValidator;
    private AdminUpdateValidator $updateValidator;

    private LoggerInterface $logger;

    public function __construct(AdminRepository $repository, LoggerFactory $loggerFactory, AdminCreateValidator $createValidator, AdminUpdateValidator $updateValidator)
    {
        $this->repository = $repository;
        $this->createValidator = $createValidator;
        $this->updateValidator = $updateValidator;

        $this->logger = $loggerFactory->addFileHandler('Admin_Panel/Admin_IP/Admins.log')->createLogger();
    }

    //Get All Data
    public function getAdmins(): AdminData
    {
        $admins = $this->repository->getAdmins();

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
            $admin->time_in = $adminRow['time_in'];
            $admin->time_out = $adminRow['time_out'];
            $admin->created_at = $adminRow['created_at'];

            $result->admins[] = $admin;
        }

        return $result;
       
    }

    //Get One Data
    public function getOneAdmin(int $adminId): AdminData
    {
        $admins = $this->repository->getOneAdmin($adminId);

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
            $admin->time_in = $adminRow['time_in'];
            $admin->time_out = $adminRow['time_out'];
            $admin->created_at = $adminRow['created_at'];

            $result->admins[] = $admin;
        }

        return $result;
    }

    //Insert Data
    public function insertAdmin(array $data, string $password): int
    {
        
        $this->createValidator->validateCreateData($data);

        $adminId = $this->repository->insertAdmin($data, $password);

        $this->logger->info(sprintf('Admin created successfully: %s', $adminId));

        return $adminId;
    }

    //Username Insert Validation
    public function unameInsertValid(string $username): int
    {
        $uname_count = $this->repository->unameInsertValid($username);

        return $uname_count;
    }

    //Email Insert Validation
    public function emailInsertvalid(string $email): int
    {
        $email_count = $this->repository->emailInsertvalid($email);

        return $email_count;
    }

    //Update Data
    public function updateAdmin(int $adminId, array $data): int
    { 
        $this->updateValidator->validateUpdateData($data);

        $admin = $this->repository->updateAdmin($adminId, $data);

        $this->logger->info(sprintf('Admin updated successfully: %s', $adminId));

        return $admin;
    }

    //Username Update Validation
    public function unameUpdateValid(string $username, int $admin_id): int
    {
        $uname_count = $this->repository->unameUpdateValid($username, $admin_id);

        return $uname_count;
    }

    //Email Update Validation
    public function emailUpdateValid(string $email, int $admin_id): int
    {
        $email_count = $this->repository->emailUpdateValid($email, $admin_id);

        return $email_count;
    }

    //block and unblock
    public function blockAdmin(int $admin_id, int $active): int
    {
        // print_r($admin_id); exit;

        $admin = $this->repository->blockAdmin($admin_id, $active);

        $this->logger->info(sprintf('Blocked or Unblocked Successfully: %s', $admin_id));

        return $admin;
    }
  

    //Delete Data
    public function deleteAdmin(int $adminId): int
    {
        $admin = $this->repository->deleteAdmin($adminId);

        $this->logger->info(sprintf('Admin delete successfully: %s', $adminId));

        return $admin;
    }

   

}
