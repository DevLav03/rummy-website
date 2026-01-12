<?php

namespace App\Domain\Admin_Panel\Promotion\Repository;

use PDO;

final class PromotionRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //get all data
    public function getPromotion(): array
    {   
        $statement = $this->conn->prepare("SELECT * FROM promotions");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows;
    }


    //get single data
    public function getOnePromotion(int $promotionId): array
    {
        $query="SELECT * FROM promotions  where id = ".$promotionId;
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows; 
    }


    
    //Insert
    public function insertPromotion(array $data, $filename ): int
    {
        //print_r($adminId); exit;
        $statement =  $this->conn->prepare("INSERT INTO promotions (title, short_description, description, promotion_image) VALUES (?,?,?,?) ");
        $statement->execute(array($data['title'],$data['short_description'],$data['description'],$filename));//, $adminId 
        $id = $this->conn->lastInsertId();
        $statement->closeCursor();
        return $id;
    }

    //Update
    public function updatePromotion(array $data, int $promotionId ,$filename ): int
    {
        //print_r($promotion_Id); exit;
        $statement =  $this->conn->prepare("UPDATE  promotions SET title=?, short_description=?, description=?, promotion_image=? WHERE id=? ");
        $statement->execute(array($data['title'], $data['short_description'], $data['description'], $filename, $promotionId ));
        $id = $this->conn->lastInsertId();
        $statement->closeCursor();
        return $id;
    }

    //Delete data
    public function deletePromotion(int $promotionId): int
    {
        $query="DELETE FROM promotions where id=".$promotionId; 
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $count = $statement->rowCount();
		$statement->closeCursor();
        return $count;
    }


    //Change Status
    public function promotionStatusById(int $promotionId, int $status): int
    {
        $statement =  $this->conn->prepare("UPDATE promotions SET status=? WHERE id=?");
        $statement->execute(array($status,$promotionId));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;    
    }
}

