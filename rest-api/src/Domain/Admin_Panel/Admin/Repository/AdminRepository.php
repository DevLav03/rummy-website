<?php

namespace App\Domain\Admin_Panel\Admin\Repository;

use PDO;

final class AdminRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //get all data
    public function getAdmins(): array
    {   
        $statement = $this->conn->prepare("SELECT va.*, vr.role_type, vr.role_name FROM admins as va INNER JOIN master_admin_roles as vr ON va.role_id = vr.role_id");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows;
    }

    //get single data
    public function getOneAdmin(int $adminId): array
    {
        $query="SELECT va.*, vr.role_type, vr.role_name FROM admins as va INNER JOIN master_admin_roles as vr ON va.role_id = vr.role_id where va.id = ".$adminId;
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows; 
    }

    //insert data
    public function insertAdmin(array $data, string $password): int
    {           
        $statement =  $this->conn->prepare("INSERT INTO admins ( name, username, password, email, phone_no, role_id) VALUES(?,?,?,?,?,?)");
        $statement->execute(array($data['name'],$data['username'],$password, $data['email'], $data['phone_no'], $data['role_id']));
        $id = $this->conn->lastInsertId();
		$statement->closeCursor();
        return $id;
    }

    //Username Insert Validation
    public function unameInsertValid(string $username): int
    {
        $query="SELECT count(id) as counts FROM admins where username = '" .$username . "'";
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        $count = $rows[0]['counts'];
        return $count; 
    }
    
   //Email Insert Validation
    public function emailInsertvalid(string $email): int
    {
        $query="SELECT count(id) as counts FROM admins where email = '" .$email . "'";
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        $count = $rows[0]['counts'];
        return $count; 
    }

    //Update data
    public function updateAdmin(int $adminId, array $admin): int
    {

        if($admin['password'] == 'null'){
            $statement =  $this->conn->prepare("UPDATE admins set name=?, username=?, email=?, phone_no=?, role_id=? where id=?");
            $statement->execute(array($admin['name'], $admin['username'],$admin['email'],$admin['phone_no'],$admin['role_id'], $adminId));
        }else{
            $statement =  $this->conn->prepare("UPDATE admins set name=?, username=?, password=?, email=?, phone_no=?, role_id=? where id=?");
            $statement->execute(array($admin['name'], $admin['username'],$admin['password'],$admin['email'],$admin['phone_no'],$admin['role_id'],$adminId));
        }

        $count = $statement->rowCount();

        $statement->closeCursor();
        
        return $count;
    }

    //Update Validate Username
    public function unameUpdateValid(string $username, int $admin_id): int
    {
        $query="SELECT count(id) as counts FROM admins WHERE username = '" .$username . "' AND id != " .$admin_id;
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();

        $count = $rows[0]['counts'];
        return $count; 
    }
    
    //Update Validate Email
    public function emailUpdateValid(string $email, int $admin_id): int
    {
        $query="SELECT count(id) as counts FROM admins WHERE email = '" .$email . "' AND id != " .$admin_id;
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();

        $count = $rows[0]['counts'];
        return $count; 
    }

    //block and unblock
    public function blockAdmin(int $admin_id, int $active): int
    { 
        $statement =  $this->conn->prepare("UPDATE admins SET active=? WHERE id=?");
        $statement->execute(array($active, $admin_id));
        $count = $statement->rowCount();
        $statement->closeCursor();
    
        return $count;
    }  
 
    //Delete data
    public function deleteAdmin(int $adminId): int
    {
        $query="DELETE FROM admins WHERE id=".$adminId; 
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $count = $statement->rowCount();
		$statement->closeCursor();
        return $count;
    }

}

