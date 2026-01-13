<?php

namespace App\Domain\Master_Table\Default_Mail\Repository;

use PDO;

final class DefaultMailRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }



    //Get all defaultmail
    public function getAllDefaultMail(): array
    {  
        $sql = "SELECT * FROM master_mail_defult_templates"; 
        $statement = $this->conn->prepare($sql); 
        $statement->execute();
        $result = [];
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    //Get one defaultmail
    public function getOneDefaultMail(int $defaultmail_id)
    {  
        $sql = "SELECT * FROM master_mail_defult_templates WHERE id= $defaultmail_id"; 
        $statement = $this->conn->prepare($sql); 
        $statement->execute();
        $result = [];
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

   //Update data
   public function updateDefaultMail(int $defaultmailId, array $data): int
   {
       
        $statement =  $this->conn->prepare("UPDATE master_mail_defult_templates SET name=?, subject=?, message=? WHERE id=? ");
        $statement->execute(array($data['name'], $data['subject'], $data['message'], $defaultmailId));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
   }


   public function getMailTemplate(string $mail_type): array
   {

       $query="SELECT * FROM master_mail_defult_templates  WHERE type_mail = '".$mail_type."'";
       $statement = $this->conn->prepare($query);
       $statement->execute();
       $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
       $statement->closeCursor();

       return $rows[0];
   }

   

   
}