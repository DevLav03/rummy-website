<?php

namespace App\Domain\Rummy_Website\Our_Games\Repository;

use PDO;

final class OurGamesRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //get all data
    public function getAllGames(): array
    {   
        $statement = $this->conn->prepare("SELECT * FROM  ws_our_games ");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows;
    }

    //get  data
    public function getGames(): array
    {   
        $statement = $this->conn->prepare("SELECT * FROM  ws_our_games WHERE status = '1'");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $rows;
    }


    //Insert
    public function insertGames(array $data, $filename ): int
    {
        
        $statement =  $this->conn->prepare("INSERT INTO ws_our_games (title,type_heading, game_heading, image) VALUES (?,?,?,?) ");
        $statement->execute(array($data['title'],$data['type_heading'],$data['game_heading'], $filename));
        $id = $this->conn->lastInsertId();
        $statement->closeCursor();
        return $id;
    }

    //Update
    public function updateGames(array $data, int $gamesId ,$filename ): int
    {
        
        $statement =  $this->conn->prepare("UPDATE  ws_our_games SET title=?,type_heading=?, game_heading=?, image=?  WHERE id=? ");
        $statement->execute(array($data['title'],$data['type_heading'],$data['game_heading'], $filename,  $gamesId ));
        $id = $this->conn->lastInsertId();
        $statement->closeCursor();
        return $id;
    }
   
    //status change
    public function changeStatus(int $games_id, int $status): int
    { 
        $statement =  $this->conn->prepare("UPDATE ws_our_games SET status=? WHERE id=?");
        $statement->execute(array($status, $games_id));
        $count = $statement->rowCount();
        $statement->closeCursor();
    
        return $count;
    }  
 
    //delete data
    public function deleteGames(int $gamesId): int
    {
        $query="DELETE FROM ws_our_games where id=".$gamesId; 
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $count = $statement->rowCount();
		$statement->closeCursor();
        return $count;
    }

}

