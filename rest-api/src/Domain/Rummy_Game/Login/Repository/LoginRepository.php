<?php

namespace App\Domain\Rummy_Game\Login\Repository;

use PDO;

final class LoginRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    //Login With Passowrd
    public function loginPassword(array $data, array $ip_info, array $ip_details): array 
    {

        $sql = 'CALL users_login_with_password(:in_user, :in_password, :in_device, :in_country, :in_state, :in_city, :in_ip)';
        $statement = $this->conn->prepare($sql);
        $statement->bindParam(':in_user', $data['mobile_or_mail'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_password', $data['password'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_device', $ip_info['device_type'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_country', $ip_details['country'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_state', $ip_details['regionName'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_city', $ip_details['city'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_ip', $ip_info['ip_address'], PDO::PARAM_STR,255);

        $statement->execute();
        $result = [];
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

       // print_r($result); exit;

        return $result;

    }     

    //Login with OTP
    public function otpLogin(array $data): int
    {
        
        $query="SELECT COUNT(id) as count FROM users WHERE phone_no = ".$data['phone_no'];
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
    
        return $rows[0]['count'];
    }

    public function stateCount(string $state){

        $query="SELECT count(id) as counts FROM master_game_states WHERE state_name = '".$state."' AND status = '1'";
        //print_r($query); exit;
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        $count = $rows[0]['counts'];
        return $count; 

    }

    //Insert OTP
    public function insertOTPcode(string $phone_no, string $otp_number): int
    {   
        $user_id = $this->getUserID($phone_no);

        $now = new \DateTime();
        $expiry_datetime = date_add($now, new \DateInterval("PT05M"))->format('Y-m-d H:i:s');

        $query = "INSERT INTO users_signin_otp_verify(user_id, mobile, mobile_otp, otp_expiry_on) VALUES(?,?,?,?)";
        $statement =  $this->conn->prepare($query);
        $statement->execute(array($user_id, $phone_no, $otp_number, $expiry_datetime));
        $id = $this->conn->lastInsertId();
		$statement->closeCursor();
        return $id;
        
    }
    
    public function getUserID(string $phone_no): int
    {   
        $query="SELECT id FROM users WHERE phone_no = ".$phone_no;
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
    
        return $rows[0]['id'];
    }

    //OTP With Verify
    public function otpLoginVerify(array $data, array $ip_info, array $ip_details): array
    {
        $now = new \DateTime();
        $current_datetime= $now->format('Y-m-d H:i:s');

        $sql = 'CALL users_login_with_otp_verify(:in_mobile_no, :in_otp_no, :in_current_date, :in_country, :in_state, :in_city, :in_ip_address, :in_device_type)';

        $statement = $this->conn->prepare($sql);
        $statement->bindParam(':in_mobile_no', $data['phone_no'], PDO::PARAM_STR,10);
        $statement->bindParam(':in_otp_no', $data['otp_no'], PDO::PARAM_INT,6);
        $statement->bindParam(':in_current_date', $current_datetime, PDO::PARAM_STR,512);
        $statement->bindParam(':in_country', $ip_details['country'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_state', $ip_details['regionName'], PDO::PARAM_STR,512);
        $statement->bindParam(':in_city', $ip_details['city'], PDO::PARAM_STR,512);
        $statement->bindParam(':in_ip_address', $ip_info['ip_address'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_device_type', $ip_info['device_type'], PDO::PARAM_STR,512);
        $statement->execute();
        $result = [];
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    } 


    //Last Login
    public function LastLogin(): array
    {   
        $statement = $this->conn->prepare("SELECT * FROM `users_log_history` WHERE action ='login' ORDER BY created_at DESC LIMIT 1");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows;
    }
    
   
}

