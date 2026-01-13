<?php

namespace App\Domain\Admin_Panel\Admin\Repository;

use PDO;

final class AdminIpRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }   
    
    //Remove Admin IP Restrict Status
    public function adminIpStatusById(int $adminId, int $status): int
    {
        $statement =  $this->conn->prepare("UPDATE admins SET ip_restrict=? WHERE id=?");
        $statement->execute(array($status,$adminId));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;    
    }

    //Get Admin IP
    public function getOneAdminIp(int $adminId): array
    {
        $query="SELECT * FROM admin_ip_list where admin_id = ".$adminId;
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $rows; 
    }
   
    //Insert Admin IP Address
    public function insertAdminIp(array $data): int
    {
        $statement = $this->conn->prepare("INSERT INTO admin_ip_list (admin_id, ip_address) VALUES(?,?)");
        $statement->execute(array($data['admin_id'], $data['ip_address']));
        $id = $this->conn->lastInsertId();
		$statement->closeCursor();
        return $id;   
    }
    public function checkInsertAdminIp(array $data): int
    {
        $query="SELECT COUNT(id) as count FROM admin_ip_list where admin_id = ".$data['admin_id']." AND ip_address = '".$data['ip_address']."'";
        //print_r($query); exit;
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();

        $count = $rows[0]['count'];
        return $count;
    }


    //Update Admin IP Address
    public function updateAdminIpById(int $id, array $data): int
    {

        $statement =  $this->conn->prepare("UPDATE admin_ip_list SET ip_address=? WHERE id=?");
        $statement->execute(array($data['ip_address'], $id));
        $count = $statement->rowCount();
        $statement->closeCursor();

        // print_r($count); exit;
        return $count;  
        
    }
    public function checkUpdateAdminIp(int $id, array $data): int
    {
        $query="SELECT COUNT(id) as count FROM admin_ip_list where admin_id = ".$data['admin_id']." AND ip_address = '".$data['ip_address']."' AND id != ".$id;
        //print_r($query); exit;
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();

        $count = $rows[0]['count'];
        return $count;
    }

    //Admin IP Status Change
    public function updateAdminIpStatus(int $adminId)
    {

        $statement =  $this->conn->prepare("UPDATE admins SET ip_restrict=? WHERE id=?");
        $statement->execute(array(1, $adminId));
        $count = $statement->rowCount();
        $statement->closeCursor();
        //return $count;  
        
    }

    //Delete Admin IP Address
    public function deleteAdminIpById(int $id): int
    {
       
        $query="DELETE FROM admin_ip_list WHERE id = ".$id; 

        $statement = $this->conn->prepare($query);
        $statement->execute();
        $count = $statement->rowCount();
		$statement->closeCursor();
        return $count;
   }

}

