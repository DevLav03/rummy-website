<?php

namespace App\Domain\Admin_Panel\Login\Repository;

use PDO;

final class LoginRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    //Login Admin
    public function adminLogin(array $data, array $ip_info): array 
    {

        //print_r($data); exit;

        $sql = 'CALL admins_login(:in_uname,:in_password,:in_ip, :in_device)';
        $statement = $this->conn->prepare($sql);
        $statement->bindParam(':in_uname', $data['username'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_password', $data['password'], PDO::PARAM_STR,500);
        $statement->bindParam(':in_ip', $ip_info['ip_address'], PDO::PARAM_STR,15);
        $statement->bindParam(':in_device', $ip_info['device_type'], PDO::PARAM_STR,512);
        $statement->execute();
        $result = [];
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        // print_r($result); exit;

        return $result;
       
    }  

    //Logout Admin   
    public function adminLogout(array $data): int
    {
        $statement =  $this->conn->prepare("INSERT INTO admin_log_history (admin_id, location_ip, login_device, action) VALUES(?,?,?,?)");
        $statement->execute(array($data['admin_id'], $data['ip_address'], $data['device_type'], $data['action']));
        $id = $this->conn->lastInsertId();
        $statement->closeCursor();
        return $id;
    }
  
    //Admin History
    public function logAdminHistory( array $data, int $admin_id ): array
    {
        if(!empty($data['search_val'])){
            $searchString=" and (aul.location_ip like '%".$data['search_val']."%' or aul.login_device like '%".$data['search_val']."%' or aul.action like '%".$data['search_val']."%') ";
        }else{
            $searchString="";
        }
        $query="SELECT au.username, aul.*, DATE(aul.created_at) AS created_date FROM admins as au LEFT JOIN admin_log_history as aul ON aul.admin_id = au.id WHERE aul.admin_id = " .$admin_id. " AND DATE(aul.created_at) BETWEEN '".$data['start_date']."' AND '" .$data['end_date']. "' ".$searchString." ORDER BY created_date DESC limit ".$data['offset'].",".$data['limit'].";";
        
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
   
        return $rows;
    }

    public function logAdminHistoryCount( array $data, int $admin_id ): int
    {
        if(!empty($data['search_val'])){
            $searchString=" and (aul.location_ip like '%".$data['search_val']."%' or aul.login_device like '%".$data['search_val']."%' or aul.action like '%".$data['search_val']."%') ";
        }else{
            $searchString="";
        }
        $query="SELECT count(au.username) as total FROM admins as au LEFT JOIN admin_log_history as aul ON aul.admin_id = au.id WHERE aul.admin_id = " .$admin_id. " AND DATE(aul.created_at) BETWEEN '".$data['start_date']."' AND '" .$data['end_date']."' ".$searchString.";";
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
    
        return $rows[0]['total'];
    }
  
    //Admin Time in   
    public function timeinAdmin(int $admin_id): int
    {
        $now = new \DateTime();
        $current_datetime= $now->format('Y-m-d H:i:s');
        //print_r($admin_id); exit;

        $statement =  $this->conn->prepare("UPDATE  admins SET time_in=? WHERE id=?");
        $statement->execute(array($current_datetime, $admin_id));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
    }
    
    //Admin Time out   
    public function timeoutAdmin(int $admin_id): int
    {
        $now = new \DateTime();
        $current_datetime= $now->format('Y-m-d H:i:s');
        //print_r($data); exit;

        $statement =  $this->conn->prepare("UPDATE  admins SET time_out=? WHERE id=?");
        $statement->execute(array($current_datetime, $admin_id));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
    }
}

