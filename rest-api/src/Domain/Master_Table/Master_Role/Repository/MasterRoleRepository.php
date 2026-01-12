<?php

namespace App\Domain\Master_Table\Master_Role\Repository;

use PDO;

final class MasterRoleRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //get all data
    public function getRoles(): array
    {   
        $statement = $this->conn->prepare("SELECT * FROM master_admin_roles");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows;
    }

    //get One data
    public function getOneRole(int $role_id): array
    {   
        $statement = $this->conn->prepare("SELECT * FROM master_admin_roles WHERE role_id = ".$role_id);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $rows;
    }

    //insert role data
    public function insertRole(array $data): int
    {           
        $statement =  $this->conn->prepare("INSERT INTO master_admin_roles (role_type, role_name) VALUES (?,?)");
        $statement->execute(array($data['role_type'], $data['role_name']));
        //print_r("INSERT INTO master_admin_roles (role_name, role_type)VALUES(?,?)"); exit;
        $id = $this->conn->lastInsertId();
		$statement->closeCursor();
        return $id;
    }

    //Delete data
    public function deleteRoles(int $role_id): int
    {

        $role_count = $this->RoleCounts($role_id);

        if($role_count == 0){

            $query="DELETE FROM master_admin_roles WHERE role_id =".$role_id; 
            $statement = $this->conn->prepare($query);
            $statement->execute();
            $count = $statement->rowCount();
            $statement->closeCursor();    

            return 1;

        }else{
            return 0;
        }
    }

    public function RoleCounts(int $role_id){

        $statement = $this->conn->prepare("SELECT COUNT(ID) as count FROM admins WHERE role_id = ".$role_id);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows[0]['count'];

    }

    //Update role data
    // public function insertRole(array $data): int
    // {           
    //     $statement =  $this->conn->prepare("INSERT INTO master_admin_roles (role_type, role_name) VALUES (?,?)");
    //     $statement->execute(array($data['role_type'], $data['role_name']));
    //     //print_r("INSERT INTO master_admin_roles (role_name, role_type)VALUES(?,?)"); exit;
    //     $id = $this->conn->lastInsertId();
    //     $statement->closeCursor();
    //     return $id;
    // }

}
