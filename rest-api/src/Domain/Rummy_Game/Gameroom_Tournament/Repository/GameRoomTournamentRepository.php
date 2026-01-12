<?php

namespace App\Domain\Rummy_Game\Gameroom_Tournament\Repository;

use PDO;

final class GameRoomTournamentRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    public function getTourneyGameType(): array
    {
        $query="SELECT game_id,game_type FROM vw_master_game_table WHERE match_id = 2 GROUP BY game_type";
        $statement = $this->conn->prepare($query);
        $statement->execute();
        //print_r($query); exit;
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $rows; 
    }

    public function getTourneyMaxPlayer($game_id): array
    {
        $query="SELECT id,max_player FROM master_game_tables WHERE match_id = 2 AND game_id =" .$game_id;
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $rows; 
    }

    public function getTourneyEntryFees(int $game_id, int $max_player): array
    {
        $query="SELECT id,entry_fees FROM master_game_tables WHERE match_id = 2 AND game_id = ".$game_id." AND max_player = " .$max_player;
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $rows; 
    }


    //get all data
    public function getTourneyroom(array $data): array
    {

        // $from = $data['start_date'];
        // $to = $data['end_date'];
        //
       
        $sql = 'CALL get_tourney_rooms(:in_from_date, :in_to_date, :in_game_type, :in_table_status, :in_active_status, :in_search_val, :in_offset,:in_limit)';

        $statement = $this->conn->prepare($sql); 
        $statement->bindParam(':in_from_date', $data['start_date'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_to_date', $data['end_date'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_game_type', $data['game_type'], PDO::PARAM_STR, 255);
        $statement->bindParam(':in_table_status', $data['table_status'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_active_status', $data['active_status'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_search_val', $data['search_val'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_offset', $data['offset'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_limit', $data['limit'], PDO::PARAM_STR,255);
        $statement->execute();
        $result = [];
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        //print_r($result); exit;
        return $result;

    }   

    //insert data
    public function insertTourneyroom(array $data): int
    {           

        $game_table_id = $this->getGameId($data);
        
        $statement =  $this->conn->prepare("INSERT INTO game_room_tourney (title, start_date, start_time, reg_start_date, reg_start_time, reg_end_date, reg_end_time, price_amount, game_table_id) VALUES(?,?,?,?,?,?,?,?,?)");

        $statement->execute(array($data['title'],$data['start_date'],$data['start_time'], $data['reg_start_date'],$data['reg_start_time'],$data['reg_end_date'],$data['reg_end_time'], $data['price_amount'], $game_table_id));

        $id = $this->conn->lastInsertId();
        $statement->closeCursor();
        return $id;
    }

    public function getGameId(array $data){

        $query = "SELECT id FROM vw_master_game_table WHERE game_id = ".$data['game_id']." AND max_player = ".$data['max_player']." AND entry_fees = " .$data['entry_fees'];

        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();

        //print_r($rows); exit;

        return $rows[0]['id']; 

    }



    //Status Change
    public function TourneyroomActive(int $tourneygameId, int $active): int
    {
        $statement =  $this->conn->prepare("UPDATE game_room_tourney SET active=? WHERE id=?");
        $statement->execute(array($active, $tourneygameId));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;    
    }


    //Delete data
    public function deleteTourneyroom(int $tourneygameid): int
    {
        $query="DELETE FROM game_room_tourney where id=".$tourneygameid; 
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
    }

}
