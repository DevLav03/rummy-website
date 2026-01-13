<?php

namespace App\Domain\Master_Table\Game_Welcome_Bonus\Repository;

use PDO;

final class WelcomeBonusRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //Get All Data
    
    public function getWelcomeBonus(): array
    {   
        $statement = $this->conn->prepare("SELECT * FROM game_bonus_welcome");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows;
    }




    //Insert Data
    public function insertWelcomeBonus(array $data): int
    {           
        
        $statement =  $this->conn->prepare("INSERT INTO game_bonus_welcome (deposit_number,	bonus_type, start_cash, end_cash, bonus_percentage, maximum_bonus, instant_cash_percentage, maximum_instant, order_by, last_update, added_by, added_on, last_updated_on) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $statement->execute(array($data['deposit_number'],$data['bonus_type'],$data['start_cash'],$data['end_cash'],$data['bonus_percentage'],$data['maximum_bonus'],$data['instant_cash_percentage'],$data['maximum_instant'],$data['order_by'],$data['last_update'],$data['added_by'],$data['added_on'],$data['last_updated_on']));
        $id = $this->conn->lastInsertId();
        $statement->closeCursor();
        // print_r($statement); exit;
        return $id;
    }

   //Update Data
   public function updateWelcomeBonus(int $welcomebonusId, array $data): int
   {
        
        $statement =  $this->conn->prepare("UPDATE game_bonus_welcome SET deposit_number=?, bonus_type=?, start_cash=?, end_cash=?, bonus_percentage=?, maximum_bonus=?, instant_cash_percentage=?, maximum_instant=?, order_by=?, last_update=?, added_by=?, added_on=?, last_updated_on=?   WHERE id=?");
        $statement->execute(array($data['deposit_number'],$data['bonus_type'],$data['start_cash'],$data['end_cash'],$data['bonus_percentage'],$data['maximum_bonus'],$data['instant_cash_percentage'],$data['maximum_instant'],$data['order_by'],$data['last_update'],$data['added_by'],$data['added_on'],$data['last_updated_on'], $welcomebonusId));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
   }



    //Delete Data
    public function deleteWelcomeBonus(int $welcomebonusId): int
    {
        $query="UPDATE game_bonus_welcome SET is_deleted = 1 WHERE  id =".$welcomebonusId; 
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $count = $statement->rowCount();
		$statement->closeCursor();
        return $count;
    }


   
}
