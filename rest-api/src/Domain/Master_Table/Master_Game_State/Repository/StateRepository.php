<?php

namespace App\Domain\Master_Table\Master_Game_State\Repository;

use PDO;

final class StateRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //get all data
    public function getStates(): array
    {   
        $statement = $this->conn->prepare("SELECT * FROM master_game_states ");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows;
    }

    //get single data
    public function getOneState(int $stateId): array
    {
        $query="SELECT * FROM master_game_states  WHERE id = ".$stateId;
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows; 
    }


    //Update data
    public function updateState(int $stateid, array $data): int
    {
        
        $statement =  $this->conn->prepare("UPDATE master_game_states SET  user_alert_message=? WHERE id=?");
        $statement->execute(array($data['user_alert_message'], $stateid));      
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
    }


 
    //Status Change
    public function ChangestateStatus(int $stateid, int $status): int
    {
        $statement =  $this->conn->prepare("UPDATE master_game_states SET status=? WHERE id=?");
        $statement->execute(array($status, $stateid));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;    
    }

}

