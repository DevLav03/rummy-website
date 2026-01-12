<?php

namespace App\Domain\Admin_Panel\Profile\Repository;

use PDO;

final class ProfileRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //update profile
    public function updateProfile(int $adminId, array $admin): int
    {
        $statement =  $this->conn->prepare("update admins set username=?, name=?, email=? where id=?");
        $statement->execute(array($admin['username'],$admin['name'],$admin['email'],$adminId));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
    } 

    //Change Password
    public function getCurrentPassword(int $adminId, string $currentPassword): array
    {
        $query="SELECT * FROM admins where id=".$adminId ." and password='".$currentPassword."'"; 
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows; 
    }

    public function updatePassword(int $adminId, string $password): int
    {
        $statement =  $this->conn->prepare("update admins set password=?  where id=?");
        $statement->execute(array($password, $adminId));
        $statement->closeCursor();
        return $adminId;
    }


  
   
   
}
