<?php

namespace App\Domain\Rummy_Game\Kyc_Verify\Repository;

use PDO;

final class KycVerifyRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //Get All Data
    public function getKycverifys( array $data): array
    {
        if(!empty($data['search_val'])){
            $searchString=" and (u.name like '%".$data['search_val']."%' OR u.username like '%".$data['search_val']."%' OR u.email like '%".$data['search_val']."%' OR u.phone_no like '%".$data['search_val']."%' ) ";
        }else{
            $searchString="";
        }
        $query="SELECT udp.name, u.username,  udk.kyc_verify_status, u.email_verify_status, u.phone_verify_status,u.email, u.phone_no, ukv.* FROM users_verification_kyc as ukv  INNER JOIN users as u ON u.id = ukv.user_id  LEFT JOIN users_details_profile as udp ON udp.id = u.id LEFT JOIN users_details_kyc as udk ON udk.id = u.id WHERE DATE(ukv.pc_requested_on) BETWEEN '".$data['start_date']."' AND '" .$data['end_date']. "' ".$searchString." ORDER BY ukv.pc_requested_on DESC limit ".$data['offset'].",".$data['limit'].";";
        // print_r($query); exit;
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        
        return $rows;
    }


    //Get One Data
    public function getUserKycVerify(array $data, int $userId): array
    {

        $query="SELECT * FROM users_details_kyc WHERE user_id = $userId";
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows;
    }

    //KycVerifyCount
    public function getKycverifysCount( array $data): int
    {
        if(!empty($data['search_val'])){
            $searchString=" and (u.name like '%".$data['search_val']."%' OR u.username like '%".$data['search_val']."%' OR u.email like '%".$data['search_val']."%' OR u.phone_no like '%".$data['search_val']."%') ";
        }else{
            $searchString="";
        }
        
        $query="SELECT count(u.id) as total  FROM users_verification_kyc as ukv  INNER JOIN users as u ON u.id = ukv.user_id  WHERE DATE(ukv.pc_requested_on) BETWEEN '".$data['start_date']."' AND '" .$data['end_date']."' ".$searchString.";";
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
       
        return $rows[0]['total'];
    }

    //Insert
    public function insertKycVerify(array $data,  $filename): int
    {
        $statement =  $this->conn->prepare("INSERT INTO users_verification_kyc (user_id, pan_no, pc_file) VALUES(?,?,?)");
        $statement->execute(array($data['user_id'], $data['pan_no'], $filename));
        $id = $this->conn->lastInsertId();
        $statement->closeCursor();
        return $id;
    }

    //Upload file
    public function getKycVerify(int $kycverifyId): array
    {
       
        $query="SELECT * FROM users_verification_kyc WHERE id=".$kycverifyId; 
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows; 
    }

    //Kyc Status
    public function statusKycVerify(int $kycverify_id, int $verify_status,int $admin_id, string $ip): int
    { 
        $now = new \DateTime();
        $current_datetime= $now->format('Y-m-d H:i:s');

        $statement =  $this->conn->prepare("UPDATE users_verification_kyc SET pc_verify_status=?, pc_verified_by=?, pc_verify_by_ip_address=?, pc_verified_on=? WHERE id=?");
        $statement->execute(array($verify_status, $admin_id, $ip, $current_datetime, $kycverify_id));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
    }  
   
    public function getUserDetail(int $id): array
    {
        $query="SELECT user_id FROM users_verification_kyc WHERE id =" .$id;       
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();

        $user = $this->getUser($rows);   
        return $user;
    }

    public function getUser(array $user): array
    {
        $query="SELECT id,name,username,email FROM vw_users WHERE id = " .$user[0]['user_id'];
        //$query="SELECT  * FROM users as u INNER JOIN users_details_profile as udp ON u.id = udp.user_id  WHERE id = ".$user[0]['user_id'];

        //SELECT u.id, u.name, u.username, udp.email FROM users as u INNER JOIN users_details_profile as udp ON u.id = udp.user_id; 
        //print_r($query); exit;
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        
        return $rows;
    }

    public function changeUsersStatus(int $id): int
    { 
        $statement =  $this->conn->prepare("UPDATE users_details_kyc SET kyc_verify_status=? WHERE id=?");
        $statement->execute(array(1, $id));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
    }  
}

