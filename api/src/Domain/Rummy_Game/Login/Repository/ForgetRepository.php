<?php

namespace App\Domain\Rummy_Game\Login\Repository;

use PDO;

final class ForgetRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    //Forget Email Password
    public function forgetEmailPassword(array $users, array $data, int $otp_number): int 
    {

        $now = new \DateTime();
        $expiry_datetime = date_add($now, new \DateInterval("PT05M"))->format('Y-m-d H:i:s');

        $query = "INSERT INTO users_verify_forget_password_email(user_id, email, email_otp, otp_expiry_on) VALUES(?,?,?,?)";
        $statement =  $this->conn->prepare($query);
        $statement->execute(array($users[0]['id'], $data['email'], $otp_number, $expiry_datetime));
        $id = $this->conn->lastInsertId();
		$statement->closeCursor();
        return $id;
      
    }    
    
    public function getEmailUser(array $data): array
    {

        $query="SELECT id, name, email FROM vw_users WHERE email = '".$data['email']."'";
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
    
        return $rows;

    }

    public function forgetEmailOTPVerify(array $data): array
    {

        $now = new \DateTime();
        $current_datetime= $now->format('Y-m-d H:i:s');

        $query="SELECT * FROM users_verify_forget_password_email WHERE email = '".$data['email']."' AND email_otp = " .$data['otp_no']. " AND otp_expiry_on >= '".$current_datetime."'";
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();

        return $rows;

    }

    public function updateEmailVerify(array $ip_info, int $id)
    {
        $now = new \DateTime();
        $current_datetime= $now->format('Y-m-d H:i:s');

        $statement =  $this->conn->prepare("UPDATE users_verify_forget_password_email SET otp_verify_on=?, otp_verify_status=?, device_id=?, device_details=? WHERE id=?");
        $statement->execute(array($current_datetime, 1, $ip_info['ip_address'], $ip_info['device_type'], $id));
        $statement->closeCursor();      
    }

    //Forget Mobile Number Password
    public function forgetMobilePassword(array $users, array $data, int $otp_number): int 
    {
        $now = new \DateTime();
        $expiry_datetime = date_add($now, new \DateInterval("PT05M"))->format('Y-m-d H:i:s');

        $query = "INSERT INTO users_verify_forget_password_mobile(user_id, phone_no, mobile_otp, otp_expiry_on) VALUES(?,?,?,?)";
        $statement =  $this->conn->prepare($query);
        $statement->execute(array($users[0]['id'], $data['phone_no'], $otp_number, $expiry_datetime));
        $id = $this->conn->lastInsertId();
        $statement->closeCursor();
        return $id;
    
    }    
    
    public function getMobileUser(array $data): array
    {

        $query="SELECT id,name,phone_no FROM vw_users WHERE phone_no = '".$data['phone_no']."'";
       
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();

        return $rows;

    }

    public function forgetMobileOTPVerify(array $data): array
    {

        $now = new \DateTime();
        $current_datetime= $now->format('Y-m-d H:i:s');

        $query="SELECT * FROM users_verify_forget_password_mobile WHERE phone_no = '".$data['phone_no']."' AND mobile_otp = " .$data['otp_no']. " AND otp_expiry_on >= '".$current_datetime."'";
        
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();

        return $rows;

    }

    public function updateMobileVerify(array $ip_info, int $id)
    {
        $now = new \DateTime();
        $current_datetime= $now->format('Y-m-d H:i:s');

        $statement =  $this->conn->prepare("UPDATE users_verify_forget_password_mobile SET otp_verify_on=?, otp_verify_status=?, device_id=?, device_details=? WHERE id=?");
        $statement->execute(array($current_datetime, 1, $ip_info['ip_address'], $ip_info['device_type'], $id));
        $statement->closeCursor();      
    }

    //Reset Password
    public function resetPassword(array $data, string $password): int
    {
        $statement =  $this->conn->prepare("UPDATE users SET password=?  WHERE id=?");
        $statement->execute(array($password, $data['id']));
        $statement->closeCursor();
        return $data['id'];
    }
 
 


   
}

