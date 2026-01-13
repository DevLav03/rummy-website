<?php

namespace App\Domain\Rummy_Game\Login\Repository;

use PDO;

final class GoogleLoginRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function googleLogin(array $data, array $ip_info, array $ip_details, string $user_ref_code): array
    {   


        $sql = 'CALL users_login_with_google(:in_email,:in_device,:in_ip, :in_country, :in_state, :in_city, :in_ref_code)';
        $statement = $this->conn->prepare($sql);
        $statement->bindParam(':in_email', $data['email'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_device', $ip_info['device_type'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_ip', $ip_info['ip_address'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_country', $ip_details['country'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_state', $ip_details['regionName'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_city', $ip_details['city'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_ref_code', $user_ref_code, PDO::PARAM_STR,255);

        $statement->execute();
        $result = [];
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $result;
          
    }

    public function userLogout(int $userId, array $ip_info, array $ip_details): int
    {   

        $query = "INSERT INTO users_log_history(user_id, login_device, country_name, state_name, city_name, action, location_ip) VALUES(?,?,?,?,?,?,?)";
        $statement =  $this->conn->prepare($query);
        $statement->execute(array($userId, $ip_info['device_type'], $ip_details['country'], $ip_details['regionName'], $ip_details['city'], 'Logout', $ip_info['ip_address']));
        $id = $this->conn->lastInsertId();
		$statement->closeCursor();
        return $id;
          
    }
 
}

