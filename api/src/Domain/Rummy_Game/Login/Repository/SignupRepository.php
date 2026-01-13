<?php

namespace App\Domain\Rummy_Game\Login\Repository;

use PDO;

final class SignupRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function usersRegsiter(array $data){

        $query="SELECT count(id) as counts FROM users where phone_no = '" .$data['phone_no'] . "'";
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        $count = $rows[0]['counts'];
        return $count; 

    }

    public function stateCount(string $state){

        $query="SELECT count(id) as counts FROM master_game_states where state_name = '".$state."' AND status = '1'";
        //print_r($query); exit;
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        $count = $rows[0]['counts'];
        return $count; 

    }

    public function insertOTPcode(string $phone_no, int $otp_number): int
    {

        $now = new \DateTime();
        $expiry_datetime = date_add($now, new \DateInterval("PT05M"))->format('Y-m-d H:i:s');

        $query = "INSERT INTO users_signup_otp_verify(mobile, mobile_otp, otp_expiry_on) VALUES(?,?,?)";
        $statement =  $this->conn->prepare($query);
        $statement->execute(array($phone_no, $otp_number, $expiry_datetime));
        $id = $this->conn->lastInsertId();
		$statement->closeCursor();
        return $id;

    }

    public function usersRegsiterVerify(array $data, array $ip_info, array $ip_details, string $user_ref_code): array
    {

        $now = new \DateTime();
        $current_datetime= $now->format('Y-m-d H:i:s');

        $sql = 'CALL users_signup_otp_verify(:in_phone_no, :in_ref_code, :in_otp_code, :in_current_date, :in_ip_address, :in_device_type, :in_country, :in_state, :in_city, :in_user_ref_code)';
        $statement = $this->conn->prepare($sql);
        $statement->bindParam(':in_phone_no', $data['phone_no'], PDO::PARAM_STR,10);
        $statement->bindParam(':in_ref_code', $data['ref_code'], PDO::PARAM_STR,120);
        $statement->bindParam(':in_otp_code', $data['otp_no'], PDO::PARAM_STR,120);
        $statement->bindParam(':in_current_date', $current_datetime, PDO::PARAM_STR,512);
        $statement->bindParam(':in_ip_address', $ip_info['ip_address'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_device_type', $ip_info['device_type'], PDO::PARAM_STR,500);
        $statement->bindParam(':in_country', $ip_details['country'], PDO::PARAM_STR,15);
        $statement->bindParam(':in_state', $ip_details['regionName'], PDO::PARAM_STR,512);
        $statement->bindParam(':in_city', $ip_details['city'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_user_ref_code', $user_ref_code, PDO::PARAM_STR,120);
        $statement->execute();
        $result = [];
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $result;

    }


   

}

