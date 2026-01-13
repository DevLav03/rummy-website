<?php

namespace App\Domain\Rummy_Game\Profile\Repository;

use PDO;

final class ProfileVerifyRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //Mobile Verify
    public function mobileSendOTP(string $mobile_num, int $userId, int $otp_number): int
    {

        $now = new \DateTime();
        $expiry_datetime = date_add($now, new \DateInterval('PT05M'))->format('Y-m-d H:i:s');

        $statement =  $this->conn->prepare("INSERT INTO users_verification_phone_no (user_id, mobile, mobile_otp, otp_expiry_on) VALUES(?,?,?,?)");
        $statement->execute(array($userId, $mobile_num, $otp_number, $expiry_datetime));
        $id = $this->conn->lastInsertId();
		$statement->closeCursor();
        return $id;
        
    }

    public function mobileCount(string $mobile_num, int $userId): array
    {
        $query="SELECT * FROM users WHERE phone_no = " .$mobile_num. " AND id != " .$userId;
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
   
        return $rows;
    }

    public function mobileOTPVerify(array $data, int $userId, array $ip_info): array
    {

        $now = new \DateTime();
        $current_datetime= $now->format('Y-m-d H:i:s');

        $sql = 'CALL send_mobile_otp_verification(:in_mobile_no, :in_otp_no, :in_user_id, :in_current_date, :in_device_ip, :in_device_info)';
        $statement = $this->conn->prepare($sql);
        $statement->bindParam(':in_mobile_no', $data['phone_no'], PDO::PARAM_STR,10);
        $statement->bindParam(':in_otp_no', $data['otp_no'], PDO::PARAM_INT,6);
        $statement->bindParam(':in_user_id', $userId, PDO::PARAM_INT,11);
        $statement->bindParam(':in_current_date', $current_datetime, PDO::PARAM_STR,512);
        $statement->bindParam(':in_device_ip', $ip_info['ip_address'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_device_info', $ip_info['device_type'], PDO::PARAM_STR,512);
        $statement->execute();
        $result = [];
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $result;

    }

    //Email Verify
    public function emailSendOTP(array $data, int $userId, int $otp_number): int
    {

        $now = new \DateTime();
        $expiry_datetime = date_add($now, new \DateInterval('PT05M'))->format('Y-m-d H:i:s');

        $statement =  $this->conn->prepare("INSERT INTO users_verification_email(user_id, email, otp_no, otp_expiry_on) VALUES(?,?,?,?)");
        $statement->execute(array($userId, $data['email'], $otp_number, $expiry_datetime));
        $id = $this->conn->lastInsertId();
		$statement->closeCursor();
        return $id;
        
    }

    public function emailCount(array $data, int $userId): array
    {
        $query="SELECT * FROM users WHERE email = '".$data['email']."' AND id != " .$userId;
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
   
        return $rows;
    }

    public function emailOTPVerify(array $data, int $userId, array $ip_info): array
    {

        $now = new \DateTime();
        $current_datetime= $now->format('Y-m-d H:i:s');

        $sql = 'CALL send_email_otp_verification(:in_email, :in_otp_no, :in_user_id, :in_current_date, :in_device_ip, :in_device_info)';
        $statement = $this->conn->prepare($sql);
        $statement->bindParam(':in_email', $data['email'], PDO::PARAM_STR,10);
        $statement->bindParam(':in_otp_no', $data['otp_no'], PDO::PARAM_INT,6);
        $statement->bindParam(':in_user_id', $userId, PDO::PARAM_INT,11);
        $statement->bindParam(':in_current_date', $current_datetime, PDO::PARAM_STR,512);
        $statement->bindParam(':in_device_ip', $ip_info['ip_address'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_device_info', $ip_info['device_type'], PDO::PARAM_STR,512);
        $statement->execute();
        $result = [];
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $result;

    }

    public function getUser(int $user_id): array
    {
        $query="SELECT name FROM users_details_profile WHERE user_id = ".$user_id;
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();

        return $rows;
    }
 
}