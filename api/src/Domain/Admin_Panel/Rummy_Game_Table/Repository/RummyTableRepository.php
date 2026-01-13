<?php

namespace App\Domain\Admin_Panel\Rummy_Game_Table\Repository;

use PDO;

final class RummyTableRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //insert data
    public function insertRummytable(array $rummytable): int
    {   

        $sql = 'CALL add_rummy_table(:in_game,:in_game_type_id,:in_table_name, :in_table_no, :in_bet_value, :in_point_value, :in_sitting_capacity, :in_game_deck, :in_table_status)';
        $statement = $this->conn->prepare($sql);
        $statement->bindParam(':in_game', $rummytable['game'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_game_type_id', $rummytable['game_type_id'], PDO::PARAM_INT,11);
        $statement->bindParam(':in_table_name', $rummytable['table_name'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_table_no', $rummytable['table_no'], PDO::PARAM_INT,11);
        $statement->bindParam(':in_bet_value', $rummytable['bet_value'], PDO::PARAM_INT,11);
        $statement->bindParam(':in_point_value', $rummytable['point_value'], PDO::PARAM_INT,11);
        $statement->bindParam(':in_sitting_capacity', $rummytable['sitting_capacity'], PDO::PARAM_INT,11);
        $statement->bindParam(':in_game_deck', $rummytable['game_deck'], PDO::PARAM_INT,11);
        $statement->bindParam(':in_table_status', $rummytable['table_status'], PDO::PARAM_INT,11);
        $statement->execute();
        $result = [];
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        $id = $result[0]['id']; 
       
        return $id;
    }

    //Change status
    public function changeStatus(int $id, int $status): int
    { 
        $statement =  $this->conn->prepare("UPDATE rummy_real_money_table SET table_status=? WHERE id=?");
        $statement->execute(array($status, $id));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
    }  

    //Delete data
    public function deleteTable(int $id): int
    {
        $statement =  $this->conn->prepare("UPDATE rummy_real_money_table SET is_deleted=? WHERE id=?");
        $statement->execute(array(1, $id));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
    }


}
