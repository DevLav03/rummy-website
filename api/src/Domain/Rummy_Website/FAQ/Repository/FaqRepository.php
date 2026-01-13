<?php

namespace App\Domain\Rummy_Website\FAQ\Repository;

use PDO;

final class FaqRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //Get all data
    public function getAllFaq(): array
    {   
        $statement = $this->conn->prepare("SELECT * FROM  ws_faq");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows;
    }

    //Get data
    public function getFaq(): array
    {   
        $statement = $this->conn->prepare("SELECT * FROM  ws_faq WHERE status = '1'");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows;
    }

    //Get Latest Data
    public function getLatestFaq(): array
    {   
        $statement = $this->conn->prepare("SELECT id,title,answer,status,DATE(created_at) as created_at FROM ws_faq  LIMIT 0,5");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $rows;
    }
    
    //Insert data
    public function insertFaq(array $data): int
    {           
        $statement =  $this->conn->prepare("INSERT INTO ws_faq ( title, answer) VALUES(?,?)");
        $statement->execute(array($data['title'],$data['answer']));
        $id = $this->conn->lastInsertId();
        $statement->closeCursor();
        return $id;
    }
   
    //Update data
    public function updateFaq(int $faqId, array $data): int
    {

        $statement =  $this->conn->prepare("UPDATE ws_faq set title=?, answer=? where id=?");
        $statement->execute(array($data['title'], $data['answer'], $faqId));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
    }

    //status change
    public function changeStatus(int $faq_id, int $status): int
    { 
        $statement =  $this->conn->prepare("UPDATE ws_faq SET status=? WHERE id=?");
        $statement->execute(array($status, $faq_id));
        $count = $statement->rowCount();
        $statement->closeCursor();
    
        return $count;
    }  
 
    //delete data
    public function deleteFaq(int $faqId): int
    {
        $query="DELETE FROM ws_faq where id=".$faqId; 
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $count = $statement->rowCount();
		$statement->closeCursor();
        return $count;
    }

}

