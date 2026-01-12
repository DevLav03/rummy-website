<?php

namespace App\Domain\Master_Table\Admin_Scope\Service;

//Repository
use App\Domain\Master_Table\Admin_Scope\Repository\AdminScopeRepository;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;


final class AdminScopeService
{
    private AdminScopeRepository $repository;

    public function __construct(AdminScopeRepository $repository) 
    {
        $this->repository = $repository;
    }

    //Get All Data
    public function getAllScope(): array
    {
        $scope = $this->repository->getAllScope();
    
        return $scope;
        
    }

    //Update role Scope
    public function updateRoleScope(array $data, int $role_id): int
    {
        $scope = $this->repository->updateRoleScope($data, $role_id);
    
        return $scope;
        
    }



  
   

}
