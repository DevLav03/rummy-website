<?php

namespace App\Domain\Admin_Panel\Tournament\Repository;

use PDO;

final class TournamentRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //get all data
    public function getTournaments( array $data): array
    {
        if(!empty($data['search_val'])){
            $searchString=" and (title like '%".$data['search_val']."%') ";
        }else{
            $searchString="";
        }
        $query="SELECT * FROM game_room_tourney WHERE DATE(start_date) BETWEEN '".$data['start_date']."' AND '" .$data['end_date']. "' ".$searchString." ORDER BY start_date DESC limit ".$data['offset'].",".$data['limit'].";";
        
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
   
        return $rows;
    }

    public function getTournamentsCount( array $data): int
    {
        if(!empty($data['search_val'])){
            $searchString=" and (title like '%".$data['search_val']."%') ";
        }else{
            $searchString="";
        }
        $query="SELECT count(id) as total FROM game_room_tourney WHERE DATE(start_date) BETWEEN '".$data['start_date']."' AND '" .$data['end_date']."' ".$searchString.";";
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
    
        return $rows[0]['total'];
    }

    //get one data
    public function getOneTournament(int $tournamentId): array
    {
        $query="SELECT * FROM game_room_tourney where id=".$tournamentId; 
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $rows; 
    }

    //insert data
    public function insertTournament(array $tournament): int
    {   
        $statement =  $this->conn->prepare("INSERT INTO game_room_tourney (title,   start_date,  start_time, reg_start_date, reg_start_time, reg_end_date, reg_end_time,  price_amount, game_table_id) VALUES(?,?,?,?,?,?,?,?,?)");
        $statement->execute(array($tournament['title'],$tournament['start_date'], $tournament['start_time'], $tournament['reg_start_date'], $tournament['reg_start_time'], $tournament['reg_end_date'],$tournament['reg_end_time'], $tournament['price_amount'], $tournament['game_table_id']));
        $id = $this->conn->lastInsertId();
		$statement->closeCursor();
        return $id;       
    }

    //update data
    public function updateTournament(int $tournamentId, array $tournament): int
    {
        $statement =  $this->conn->prepare("UPDATE game_room_tourney SET title=?,  start_date=?, start_time=?, reg_start_date=?, reg_start_time=?, reg_end_date=?, reg_end_time=?, price_amount=?, game_table_id=?  WHERE id=?");
        $statement->execute(array($tournament['title'], $tournament['start_date'], $tournament['start_time'], $tournament['reg_start_date'], $tournament['reg_start_time'], $tournament['reg_end_date'], $tournament['reg_end_time'], $tournament['price_amount'],$tournament['game_table_id'],$tournamentId));
        $statement->closeCursor();
        $count = $statement->rowCount();
        //print_r($count); exit;
        return $count;
        
    }

    //block and unblock
    public function blockTournamentById(int $tournament_id, int $tourney_status): int
    { 
        //print_r($tourney_status); exit;
        $statement =  $this->conn->prepare("UPDATE game_room_tourney SET status=? WHERE id=?");
        $statement->execute(array($tourney_status, $tournament_id));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
    }  
}
