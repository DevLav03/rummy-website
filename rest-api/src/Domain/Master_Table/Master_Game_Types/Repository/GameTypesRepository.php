<?php

namespace App\Domain\Master_Table\Master_Game_Types\Repository;

use PDO;

final class GameTypesRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //get all data
    public function getGameTypes(): array
    {   
        $statement = $this->conn->prepare("SELECT * FROM master_rummy_type");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows;
    }


    //insert data
    public function insertGameType(array $data): int
    {           
        $statement =  $this->conn->prepare("INSERT INTO master_rummy_type ( name, description) VALUES(?,?)");
        $statement->execute(array($data['name'],$data['description']));
        $id = $this->conn->lastInsertId();
        $statement->closeCursor();
        return $id;
    }

   //Update data
   public function updateGameType(int $gameid, array $data): int

    {
        $statement =  $this->conn->prepare("UPDATE master_rummy_type SET name=?, description=? WHERE id=?");
        $statement->execute(array($data['name'], $data['description'], $gameid));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
    }

    //Status Change
    public function gameStatus(int $gameId, int $status): int
    {
        $statement =  $this->conn->prepare("UPDATE master_rummy_type SET active=? WHERE id=?");
        $statement->execute(array($status,$gameId));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;    
    }

    //Delete data
    public function deleteGameType(int $gameId): int
    {
        $query="DELETE FROM master_rummy_type where id=".$gameId; 
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
    }

}
