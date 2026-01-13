<?php

namespace App\Domain\Master_Table\Master_Role\Service;

//Data
use App\Domain\Master_Table\Master_Role\Data\MasterRoleData;
use App\Domain\Master_Table\Master_Role\Data\MasterRoleDataRead;

use App\Domain\Master_Table\Master_Role\Data\MasterRoleMenuData;
use App\Domain\Master_Table\Master_Role\Data\MasterRoleMenuDataRead;

//Repository
use App\Domain\Master_Table\Master_Role\Repository\MasterRoleRepository;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;

final class MasterRoleService
{
    private MasterRoleRepository $repository;
    private LoggerInterface $logger;

    public function __construct(MasterRoleRepository $repository, LoggerFactory $loggerFactory)
    {
        $this->repository = $repository;
        $this->logger = $loggerFactory->addFileHandler('Master_Table/Master_Role/Master_role.log')->createLogger();
    }

    //Get All Data
    public function getRoles(): MasterRoleData
    {
        $roles = $this->repository->getRoles();

        $result = new MasterRoleData();

        foreach ($roles as $roleRow) {
            $role = new MasterRoleDataRead();
            $role->role_id  = $roleRow['role_id'];
            $role->role_type = $roleRow['role_type'];
            $role->role_name = $roleRow['role_name'];
            $role->menu_list = $roleRow['menu_list'];
            $role->scope_list = $roleRow['scope_list'];

            $result->roles[] = $role;

        }    

        return $result;
      
    }

    //Get Role menu Data
    public function getOneRole(int $role_id): MasterRoleData
    {

        $roles = $this->repository->getOneRole($role_id);

        $result = new MasterRoleData();

        foreach ($roles as $roleRow) {
            $role = new MasterRoleDataRead();
            $role->role_id  = $roleRow['role_id'];
            $role->role_type = $roleRow['role_type'];
            $role->role_name = $roleRow['role_name'];
            $role->menu_list = $roleRow['menu_list'];
            $role->scope_list = $roleRow['scope_list'];
            
            $result->roles[] = $role;
        }

        return $result;
      
    }

    //Insert Data
    public function insertRole(array $data): int
    {

        $roles = $this->repository->insertRole($data);

        $this->logger->info(sprintf('Role created successfully: %s', $roles));

        return $roles;
    }

    //Delete Data
    public function deleteRoles(int $role_id): int
    {

       // print_r($role_id); exit;

        $roles = $this->repository->deleteRoles($role_id);

        $this->logger->info(sprintf('Role delete successfully: %s', $role_id));

        return $roles;
    }
 
    
}
