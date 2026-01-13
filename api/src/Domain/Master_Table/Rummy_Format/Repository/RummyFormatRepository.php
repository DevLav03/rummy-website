<?php

namespace App\Domain\Master_Table\Rummy_Format\Repository;

use PDO;

final class RummyFormatRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //get all data
    public function getRummyFormat(): array
    {   
        $statement = $this->conn->prepare("SELECT * FROM master_rummy_format");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows;
    }

    //get active data
    public function getActiveRummyFormat(): array
    {   
        $statement = $this->conn->prepare("SELECT * FROM master_rummy_format WHERE active = '1'");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $rows;
    }


    //insert data
    public function insertRummyFormat(array $data): int
    {           
        $statement =  $this->conn->prepare("INSERT INTO master_rummy_format (name, discription) VALUES(?,?)");
        $statement->execute(array($data['name'],$data['description']));
        $id = $this->conn->lastInsertId();
        $statement->closeCursor();
        return $id;
    }

   //Update data
   public function updateRummyFormat(int $formatId, array $data): int

    {
        $statement =  $this->conn->prepare("UPDATE master_rummy_format SET name=?, discription=? WHERE id=?");
        $statement->execute(array($data['name'], $data['description'], $formatId));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
    }

    //Status Change
    public function rummyFormatStatus(int $formatId, int $status): int
    {
        $statement =  $this->conn->prepare("UPDATE master_rummy_format SET active=? WHERE id=?");
        $statement->execute(array($status, $formatId));
        $count = $statement->rowCount();
        $statement->closeCursor();

        return $count;    
    }

    //Delete data
    public function deleteRummyFormat(int $formatId): int
    {
        $query="DELETE FROM master_rummy_format where id=".$formatId; 
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
    }

}
