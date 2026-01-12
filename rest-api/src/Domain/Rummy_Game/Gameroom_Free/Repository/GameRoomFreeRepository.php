<?php

namespace App\Domain\Rummy_Game\Gameroom_Free\Repository;

use PDO;

final class GameRoomFreeRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //get all data
    
    public function getGamefreeroom(array $data): array
    {
        //print_r($data); exit;

        $sql = 'CALL get_free_cash_room(:in_game_type, :in_sitting_capacity,  :in_joker_type, :in_deck,  :in_bet_value, :in_table_status,:in_search_val,:in_offset,:in_limit)';
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
    public function insertfreeGameroom(array $data): int
    {           
        
        $statement =  $this->conn->prepare("INSERT INTO game_room_free ( game_table_id, joker_type, deck, total_bet) VALUES(?,?,?,?)");
        $statement->execute(array($data['game_table_id'],$data['joker_type'], $data['deck'], $data['total_bet']));
        $id = $this->conn->lastInsertId();
        $statement->closeCursor();
        return $id;
    }

 

   //Update data
   public function updatefreeGameroom(int $freegameid, array $data): int
   {
       
        $statement =  $this->conn->prepare("UPDATE game_room_free SET game_table_id=?, joker_type=?, deck=?, total_bet=?  WHERE id=?");
        $statement->execute(array($data['game_table_id'], $data['joker_type'], $data['deck'], $data['total_bet'], $freegameid));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
   }

   

    //Status Change
    public function gamefreeroomStatus(int $freegameId, int $status): int
    {
        $statement =  $this->conn->prepare("UPDATE game_room_free SET active=? WHERE id=?");
        $statement->execute(array($status, $freegameId));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;    
    }


    //Delete data
    public function deletefreeGameroom(int $freegameid): int
    {
        $query="DELETE FROM game_room_free where id=".$freegameid; 
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
    }

}
