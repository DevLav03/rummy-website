<?php

namespace App\Domain\Master_Table\Rummy_Max_Seats\Repository;

use PDO;

final class RummyMaxSeatsRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //get all data
    public function getMaxSeats(): array
    {   
        $statement = $this->conn->prepare("SELECT * FROM master_rummy_max_seats");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows;
    }

    //get active data
    public function getActiveMaxSeats(): array
    {   
        $statement = $this->conn->prepare("SELECT * FROM master_rummy_max_seats WHERE active = '1'");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $rows;
    }

   

    //insert data
    public function insertMaxSeats(array $data): int
    {           
        $statement =  $this->conn->prepare("INSERT INTO master_rummy_max_seats ( seats) VALUES(?)");
        $statement->execute(array($data['seats']));
        $id = $this->conn->lastInsertId();
        $statement->closeCursor();
        return $id;
    }

   //Update data
   public function updateMaxSeats(int $seatsId, array $data): int
   {
       
        $statement =  $this->conn->prepare("UPDATE master_rummy_max_seats SET seats=? WHERE id=?");
        $statement->execute(array($data['seats'], $seatsId));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
   }

    //Status Change
    public function maxSeatsStatus(int $seatsId, int $status): int
    {
        $statement =  $this->conn->prepare("UPDATE master_rummy_max_seats SET active=? WHERE id=?");
        $statement->execute(array($status, $seatsId));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;    
    }


    //Delete data
    public function deleteMaxSeats(int $seatsId): int
    {
        $query="DELETE FROM master_rummy_max_seats where id=".$seatsId; 
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
    }

}
