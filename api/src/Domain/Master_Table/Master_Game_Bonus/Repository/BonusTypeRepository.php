<?php

namespace App\Domain\Master_Table\Master_Game_Bonus\Repository;

use PDO;

final class BonusTypeRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //get all data
    
    public function getBonusType(): array
    {   
        $statement = $this->conn->prepare("SELECT * FROM master_game_bonus_type");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows;
    }




    //insert data
    public function insertBonusType(array $data): int
    {           
        $statement =  $this->conn->prepare("INSERT INTO master_game_bonus_type ( name) VALUES(?)");
        $statement->execute(array($data['name']));
        $id = $this->conn->lastInsertId();
        $statement->closeCursor();
        return $id;
    }

   //Update data
   public function updateBonusType(int $bonustypeId, array $data): int
   {
       
        $statement =  $this->conn->prepare("UPDATE master_game_bonus_type SET name=?  WHERE id=?");
        $statement->execute(array($data['name'], $bonustypeId));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
   }



    //Status Change
    public function bonustypeStatus(int $bonustypeId, int $status): int
    {
        $statement =  $this->conn->prepare("UPDATE master_game_bonus_type SET status=? WHERE id=?");
        $statement->execute(array($status, $bonustypeId));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;    
    }


   
}
