<?php

namespace App\Domain\Master_Table\Default_Sms\Repository;

use PDO;

final class DefaultSmsRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }



    //Get all defaultsms
    public function getAllDefaultSms(): array
    {  
        $sql = "SELECT * FROM master_sms_default_templates"; 
        $statement = $this->conn->prepare($sql); 
        $statement->execute();
        $result = [];
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    //Get one defaultsms
    public function getOneDefaultSms(int $defaultsms_id)
    {  
        $sql = "SELECT * FROM master_sms_default_templates WHERE id= $defaultsms_id"; 
        $statement = $this->conn->prepare($sql); 
        $statement->execute();
        $result = [];
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

   //Update data
   public function updateDefaultSms(int $defaultsmsId, array $data): int
   {
       
        $statement =  $this->conn->prepare("UPDATE master_sms_default_templates SET name=?, subject=?, message=? WHERE id=? ");
        $statement->execute(array($data['name'], $data['subject'], $data['message'], $defaultsmsId));
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