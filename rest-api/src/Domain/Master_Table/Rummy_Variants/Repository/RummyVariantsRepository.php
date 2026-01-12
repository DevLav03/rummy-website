<?php

namespace App\Domain\Master_Table\Rummy_Variants\Repository;

use PDO;

final class RummyVariantsRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //get all data
    public function getRummyVariants(): array
    {   
        $statement = $this->conn->prepare("SELECT * FROM master_rummy_variants");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows;
    }

    //get active data
    public function getActiveRummyVariants(): array
    {   
        $statement = $this->conn->prepare("SELECT * FROM master_rummy_variants WHERE active = '1'");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $rows;
    }

   

    //insert data
    public function insertRummyVariants(array $data): int
    {           
        $statement =  $this->conn->prepare("INSERT INTO master_rummy_variants ( name, description) VALUES(?,?)");
        $statement->execute(array($data['name'],$data['description']));
        $id = $this->conn->lastInsertId();
        $statement->closeCursor();
        return $id;
    }

   //Update data
   public function updateRummyVariants(int $variantId, array $data): int
   {
       
        $statement =  $this->conn->prepare("UPDATE master_rummy_variants SET name=?, description=? WHERE id=?");
        $statement->execute(array($data['name'], $data['description'], $variantId));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
   }

    //Status Change
    public function rummyVariantStatus(int $variantId, int $status): int
    {
        $statement =  $this->conn->prepare("UPDATE master_rummy_variants SET active=? WHERE id=?");
        $statement->execute(array($status, $variantId));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;    
    }


    //Delete data
    public function deleteRummyVariants(int $variantId): int
    {
        $query="DELETE FROM master_rummy_variants where id=".$variantId; 
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
    }

}
