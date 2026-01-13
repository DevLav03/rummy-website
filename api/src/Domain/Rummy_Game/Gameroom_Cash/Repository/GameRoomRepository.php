<?php

namespace App\Domain\Rummy_Game\Gameroom_Cash\Repository;

use PDO;

final class GameRoomRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //Get Game Type
    public function getCashGameType(): array
    {
        $query="SELECT game_id,game_type FROM `vw_master_game_table` WHERE match_id = 1 GROUP BY game_type;";
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $rows; 
    }

    //Get Max Player
    public function getCashMaxPlayer($game_id): array
    {
        $query="SELECT id,max_player FROM master_game_tables WHERE match_id = 1 AND game_id =" .$game_id;
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $rows; 
    }

    //Get Entry Fees
    public function getCashEntryFees(int $game_id, int $max_player): array
    {
        $query="SELECT id,entry_fees FROM master_game_tables WHERE match_id = 1 AND game_id = ".$game_id." AND max_player = " .$max_player;
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $rows; 
    }

    //get all data
    public function getGameroom(array $data): array
    {
        //print_r($data); exit;

        $sql = 'CALL get_real_cash_room(:in_game_type, :in_sitting_capacity,  :in_joker_type, :in_deck,  :in_bet_value, :in_table_status,:in_search_val,:in_offset,:in_limit)';
        $statement = $this->conn->prepare($sql); 
        $statement->bindParam(':in_game_type', $data['game_type'], PDO::PARAM_STR, 255);
        $statement->bindParam(':in_sitting_capacity', $data['sitting_capacity'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_joker_type', $data['joker_type'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_deck', $data['deck'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_bet_value', $data['bet_value'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_table_status', $data['table_status'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_search_val', $data['search_val'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_offset', $data['offset'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_limit', $data['limit'], PDO::PARAM_STR,255);
        $statement->execute();
        $result = [];
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        //print_r( $result); exit;

        return $result;


    }   

    //insert data
    public function getTableCount(array $data): int
    {

        $query = "SELECT count(id) as total FROM vw_game_room_cash WHERE game_id = ".$data['game_id']." AND max_player = ".$data['max_player']." AND entry_fees = " .$data['entry_fees'];
        // print_r($query); exit;
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $rows[0]['total']; 

    }

    public function insertGameroom(array $data): int
    {           
        $game_table_id = $this->getGameId($data);
        // print_r($game_table_id); exit;

        $statement =  $this->conn->prepare("INSERT INTO game_room_cash (game_table_id, joker_type, deck ) VALUES(?,?,?)");
        $statement->execute(array($game_table_id, $data['joker_type'], $data['deck']));
        
        $id = $this->conn->lastInsertId();
        $statement->closeCursor();
        return $id;
    }

    public function getGameId(array $data){

        $query = "SELECT id FROM vw_master_game_table WHERE game_id = ".$data['game_id']." AND max_player = ".$data['max_player']." AND entry_fees = " .$data['entry_fees'];
        // print_r($query); exit;
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();

        // print_r($rows); exit;

        return $rows[0]['id']; 

    }

   //Update data
   public function updateGameroom(int $gameroomid, array $data): int
   {
       
        $statement =  $this->conn->prepare("UPDATE game_room_cash SET game_table_id=?, joker_type=?, deck=?   WHERE id=?");
        $statement->execute(array($data['game_table_id'], $data['joker_type'], $data['deck'], $gameroomid));
        //print_r($statement); exit;
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
   }

   

    //Status Change
    public function gameroomStatus(int $gameroomId, int $status): int
    {
        $statement =  $this->conn->prepare("UPDATE game_room_cash SET active=? WHERE id=?");
        $statement->execute(array($status, $gameroomId));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;    
    }


    //Delete data
    public function deleteGameroom(int $gameroomId): int
    {
        $query="DELETE FROM game_room_cash where id=".$gameroomId; 
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
    }

}
