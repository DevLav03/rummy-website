<?php

namespace App\Domain\Rummy_Website\News\Repository;

use PDO;

final class NewsRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //get all data
    public function getAllNews(): array
    {   
        $statement = $this->conn->prepare("SELECT id,title,image,sub_description,content,link,status,DATE(created_at) as created_at FROM ws_news ORDER BY created_at DESC");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $rows;
    }


    //get data
    public function getNews(): array
    {   
        $statement = $this->conn->prepare("SELECT * FROM ws_news WHERE status = '1'");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows;
    }


    //Get Latest Data
    public function getLatestNews(): array
    {   
        $statement = $this->conn->prepare("SELECT id,title,image,sub_description,content,link,status,DATE(created_at) as created_at FROM ws_news  LIMIT 0,2");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $rows;
    }

    //get single data
    public function getOneNews(int $newsId): array
    {
        $query="SELECT * FROM ws_news  WHERE id = ".$newsId;
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows; 
    }

    //Insert
    public function insertNews(array $data, $filename ): int
    {
        
        $statement =  $this->conn->prepare("INSERT INTO ws_news (title, image, sub_description, content,link) VALUES (?,?,?,?,?) ");
        $statement->execute(array($data['title'], $filename, $data['sub_description'], $data['content'], $data['link']));
        $id = $this->conn->lastInsertId();
        $statement->closeCursor();
        return $id;
    }

    //Update
    public function updateNews(array $data, int $newsId ,$filename ): int
    {
        //var_dump($filename); exit;
        if(empty($filename)){
            $statement =  $this->conn->prepare("UPDATE  ws_news SET title=?, sub_description=?, content=?, link=? WHERE id=? ");
            $statement->execute(array($data['title'], $data['sub_description'], $data['content'], $data['link'], $newsId ));
        }else{
            $statement =  $this->conn->prepare("UPDATE  ws_news SET title=?, image=? ,sub_description=?, content=?, link=? WHERE id=? ");
            $statement->execute(array($data['title'], $filename, $data['sub_description'], $data['content'], $data['link'], $newsId ));
        }   
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
    }
   
    //status change
    public function changeStatus(int $news_id, int $status): int
    { 
        $statement =  $this->conn->prepare("UPDATE ws_news SET status=? WHERE id=?");
        $statement->execute(array($status, $news_id));
        $count = $statement->rowCount();
        $statement->closeCursor();
    
        return $count;
    }  
 
    //delete data
    public function deleteNews(int $newsId): int
    {
        $query="DELETE FROM ws_news where id=".$newsId; 
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $count = $statement->rowCount();
		$statement->closeCursor();
        return $count;
    }

}

