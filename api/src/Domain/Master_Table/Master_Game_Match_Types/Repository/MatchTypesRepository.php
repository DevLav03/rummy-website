<?php

namespace App\Domain\Master_Table\Master_Game_Match_Types\Repository;

use PDO;

final class MatchTypesRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //get all data
    public function getMatchType(): array
    {   
        $statement = $this->conn->prepare("SELECT * FROM master_rummy_type");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows;
    }

   

    //insert data
    public function insertMatchType(array $data): int
    {           
        $statement =  $this->conn->prepare("INSERT INTO master_rummy_type ( name, description) VALUES(?,?)");
        $statement->execute(array($data['name'],$data['description']));
        $id = $this->conn->lastInsertId();
        $statement->closeCursor();
        return $id;
    }

   //Update data
   public function updateMatchType(int $mastertypeId, array $data): int
   {
       
        $statement =  $this->conn->prepare("UPDATE master_rummy_type SET name=?, description=? WHERE type_id=?");
        $statement->execute(array($data['name'], $data['description'], $mastertypeId));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
   }

    //Status Change
    public function matchStatus(int $matchId, int $status): int
    {
        $statement =  $this->conn->prepare("UPDATE master_rummy_type SET active=? WHERE type_id=?");
        $statement->execute(array($status, $matchId));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;    
    }


    //Delete data
    public function deleteMatchType(int $matchtypeId): int
    {
        $query="DELETE FROM master_rummy_type where type_id=".$matchtypeId; 
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
    }

}
