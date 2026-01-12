<?php

namespace App\Domain\Master_Table\Admin_Scope\Repository;

use PDO;

final class AdminScopeRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //get all data
    public function getAllScope(): array
    {  
        $sql = "SELECT * FROM `master_admin_roles_scope`"; 
        
        $statement = $this->conn->prepare($sql); 
        $statement->execute();
        $result = [];
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        return $result;

    }   

    //Update Role Scope
    public function updateRoleScope(array $data, int $role_id): int
    {
        $statement =  $this->conn->prepare("UPDATE master_admin_roles SET scope_list=? where role_id=?");
        $statement->execute(array($data['scope_list'], $role_id));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
    }
 
   
}

