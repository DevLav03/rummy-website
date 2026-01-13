<?php

namespace App\Domain\Admin_Panel\Users\Repository;

use PDO;

final class UsersRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //get all data
    public function getUsers(): array
    {   
        $statement = $this->conn->prepare("SELECT udp.name, udp.profile_image, u.* FROM users as u LEFT JOIN users_details_profile as udp ON u.id = udp.user_id");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows;
    }

    
    //get one data
    public function getOneUser(int $userId): array 
    {
        
        $query="SELECT mgtl.tier_name, u.id, udp.name, udp.last_name, u.username, u.email, u.email_verify_status, u.phone_no,  u.phone_verify_status, udp.gender, udp.dateofbirth, udp.state, udp.city, udp.pin_code, udp.profile_image, u.user_tier_level, u.user_rank_level, u.user_star_level, u.premium_flag, u.active, u.online_status, u.last_action_time, u.created_at FROM users as u INNER JOIN users_details_profile as udp ON udp.user_id= u.id LEFT JOIN master_game_tier_level as mgtl ON mgtl.id = u.id WHERE u.id = $userId";
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows; 
    }

    //Get Cash Chips
    public function getCashChips(int $userId): array 
    {
        
        $query="SELECT * FROM users_cash_chips WHERE user_id = $userId";
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $rows; 
    }

    //Get Free Chips
    public function getFreeChips(int $userId): array 
    {
        
        $query="SELECT * FROM users_free_chips WHERE user_id = $userId";
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $rows; 
    }

    //Get Bonus
    public function getBonus(int $userId): array 
    {
        
        $query="SELECT * FROM users_bonus WHERE user_id = $userId";
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $rows; 
    }

    //Get Cashchips
    public function getPoints(int $userId): array 
    {
        
        $query="SELECT * FROM users_point WHERE user_id = $userId";
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $rows; 
    }

    //Get Cashchips
    public function usersGameDetails(int $userId): array 
    {
        
        $query="SELECT * FROM users_game_details WHERE user_id = $userId";
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $rows; 
    }
    
    

    //Block and Unblock
    public function activeUser(int $user_id, int $active): int
    { 
        $statement =  $this->conn->prepare("UPDATE users SET active=? WHERE id=?");
        $statement->execute(array($active, $user_id));
        $count = $statement->rowCount();
        $statement->closeCursor();
    
        return $count;
    }  

    //Users Log History
    public function userLogHistory( array $data, int $user_id ): array
    {
        if(!empty($data['search_val'])){
            $searchString=" and (aul.location_ip like '%".$data['search_val']."%' or aul.login_device like '%".$data['search_val']."%' or aul.action like '%".$data['search_val']."%') ";
        }else{
            $searchString="";
        }
        $query="SELECT au.username, aul.*, DATE(aul.created_at) AS created_date FROM users as au LEFT JOIN users_log_history as aul ON aul.user_id = au.id WHERE aul.user_id = " .$user_id. " AND DATE(aul.created_at) BETWEEN '".$data['start_date']."' AND '" .$data['end_date']. "' ".$searchString." ORDER BY created_date DESC limit ".$data['offset'].",".$data['limit'].";";
        // print_r($query); exit;
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();

        return $rows;
    }

    public function userLogHistoryCount( array $data, int $user_id ): int
    {
        if(!empty($data['search_val'])){
            $searchString=" and (aul.location_ip like '%".$data['search_val']."%' or aul.login_device like '%".$data['search_val']."%' or aul.action like '%".$data['search_val']."%') ";
        }else{
            $searchString="";
        }
        $query="SELECT count(au.username) as total FROM users as au LEFT JOIN users_log_history as aul ON aul.user_id = au.id WHERE aul.user_id = " .$user_id. " AND DATE(aul.created_at) BETWEEN '".$data['start_date']."' AND '" .$data['end_date']."' ".$searchString.";";
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
    
        return $rows[0]['total'];
    }

   

}
