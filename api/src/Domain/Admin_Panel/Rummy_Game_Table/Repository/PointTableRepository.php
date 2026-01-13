<?php

namespace App\Domain\Admin_Panel\Rummy_Game_Table\Repository;

use PDO;

final class PointTableRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //point rummy
    public function getPointrummy( array $data): array
    {
        $sql = 'CALL get_point_rummy(:in_joker_type,:in_sitting_capacity,:in_bet_value,:in_table_status,:in_game_deck,:in_search_val,:in_offset,:in_limit)';
        $statement = $this->conn->prepare($sql); 
        $statement->bindParam(':in_joker_type', $data['joker_type'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_sitting_capacity', $data['sitting_capacity'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_bet_value', $data['bet_value'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_table_status', $data['table_status'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_game_deck', $data['game_deck'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_search_val', $data['search_val'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_offset', $data['offset'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_limit', $data['limit'], PDO::PARAM_STR,255);
        $statement->execute();
        $result = [];
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $result;


    }


  
    //pool rummy
    public function getPoolrummy( array $data): array
    {
        $sql = 'CALL get_pool_rummy(:in_joker_type,:in_sitting_capacity,:in_bet_value,:in_table_status,:in_game_deck,:in_search_val,:in_offset,:in_limit)';
        $statement = $this->conn->prepare($sql); 
        $statement->bindParam(':in_joker_type', $data['joker_type'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_sitting_capacity', $data['sitting_capacity'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_bet_value', $data['bet_value'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_table_status', $data['table_status'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_game_deck', $data['game_deck'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_search_val', $data['search_val'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_offset', $data['offset'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_limit', $data['limit'], PDO::PARAM_STR,255);
        $statement->execute();
        $result = [];
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

   


    //deal rummy
    public function getDealrummy( array $data): array
    {
        $sql = 'CALL get_deal_rummy(:in_joker_type,:in_sitting_capacity,:in_bet_value,:in_table_status,:in_search_val,:in_offset,:in_limit)';
        $statement = $this->conn->prepare($sql); 
        $statement->bindParam(':in_joker_type', $data['joker_type'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_sitting_capacity', $data['sitting_capacity'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_bet_value', $data['bet_value'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_table_status', $data['table_status'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_search_val', $data['search_val'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_offset', $data['offset'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_limit', $data['limit'], PDO::PARAM_STR,255);
        $statement->execute();
        $result = [];
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $result;
       
    }

    

    //do or die rummy
    public function getDoDierummy( array $data): array
    {
        $sql = 'CALL get_do_or_die_rummy(:in_sitting_capacity,:in_bet_value,:in_table_status,:in_search_val,:in_offset,:in_limit)';
        $statement = $this->conn->prepare($sql); 
        $statement->bindParam(':in_sitting_capacity', $data['sitting_capacity'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_bet_value', $data['bet_value'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_table_status', $data['table_status'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_search_val', $data['search_val'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_offset', $data['offset'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_limit', $data['limit'], PDO::PARAM_STR,255);
        $statement->execute();
        $result = [];
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    


   
}

