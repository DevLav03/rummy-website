<?php

namespace App\Domain\Master_Table\Master_Game_Table\Repository;

use PDO;

final class GameTableRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //get all data
    public function getGameTable(array $data): array
    {
        $sql = 'CALL get_game_tables(:in_match_type, :in_game_type, :in_sitting_capacity, :in_bet_value,:in_table_status,:in_search_val,:in_offset,:in_limit)';
        $statement = $this->conn->prepare($sql); 
        $statement->bindParam(':in_match_type', $data['match_type'], PDO::PARAM_INT, 11);
        $statement->bindParam(':in_game_type', $data['game_type'], PDO::PARAM_INT, 11);
        $statement->bindParam(':in_sitting_capacity', $data['sitting_capacity'], PDO::PARAM_INT,11);
        $statement->bindParam(':in_bet_value', $data['bet_value'], PDO::PARAM_INT,11);
        $statement->bindParam(':in_table_status', $data['table_status'], PDO::PARAM_INT,11);
        $statement->bindParam(':in_search_val', $data['search_val'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_offset', $data['offset'], PDO::PARAM_INT,11);
        $statement->bindParam(':in_limit', $data['limit'], PDO::PARAM_INT,11);
        $statement->execute();
        $result = [];
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $result;


    }

    //insert data
    public function insertGameTable(array $data): int
    {           
        $statement =  $this->conn->prepare("INSERT INTO master_rummy_main_rooms(format_type_id, max_seat, entry_chips, comm_per) VALUES(?,?,?,?)");
        $statement->execute(array($data['format_type_id'],$data['max_seat'], $data['entry_fees'], $data['comm_per']));
        $id = $this->conn->lastInsertId();
        $statement->closeCursor();
        return $id;
    }

    //Match Insert Validation
    public function matchInsertValid(array $data): int
    {
        $query="SELECT count(id) as counts FROM master_rummy_main_rooms where format_type_id=".$data['format_type_id']." AND max_seat=".$data['max_seat']." AND entry_chips=".$data['entry_fees'];
        //print_r($query); exit;
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        $count = $rows[0]['counts'];
        return $count; 
    }

   //Update data
   public function updateGameTable(int $gametableId, array $data): int
   {
       
        $statement =  $this->conn->prepare("UPDATE master_rummy_main_rooms SET format_type_id=?, max_seat=?, entry_chips=?, comm_per=? WHERE id=?");
        $statement->execute(array($data['format_type_id'], $data['max_seat'], $data['entry_fees'], $data['comm_per'], $gametableId));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
   }

   //Match Update Validate 
   public function matchUpdateValid(array $data, int $gametable_id): int
    {
       $query="SELECT count(id) as counts FROM master_rummy_main_rooms where format_type_id=".$data['format_type_id']." AND max_seat=".$data['max_seat']." AND entry_chips=".$data['entry_fees']." AND id != " .$gametable_id;
       $statement = $this->conn->prepare($query);
       $statement->execute();
       $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
       $statement->closeCursor();
       $count = $rows[0]['counts'];
       return $count; 
    }

    //Status Change
    public function gametableStatus(int $gametableId, int $status): int
    {
        $statement =  $this->conn->prepare("UPDATE master_rummy_main_rooms SET active=? WHERE room_id=?");
        $statement->execute(array($status, $gametableId));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;    
    }


    //Delete data
    public function deleteGameTable(int $gametableId): int
    {
        $query="DELETE FROM master_rummy_main_rooms where room_id=".$gametableId; 
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
    }

}
