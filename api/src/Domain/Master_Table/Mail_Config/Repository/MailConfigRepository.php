<?php

namespace App\Domain\Master_Table\Mail_Config\Repository;

use PDO;

final class MailConfigRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //get all data
    public function getMailConfig(): array
    {   
        $statement = $this->conn->prepare("SELECT * FROM master_config_mail");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows;
    }

    //update  data
    public function updateMailConfig(array $admin): int
    {
     
        $statement =  $this->conn->prepare("UPDATE master_config_mail SET sender_mail=?, from_name=?, smtp_host=?, smtp_type=?, smtp_port=?, smtp_username=?,smtp_password=?,smtp_auth=? ");
        $statement->execute(array($admin['sender_mail'], $admin['from_name'],$admin['smtp_host'],$admin['smtp_type'],$admin['smtp_port'],$admin['smtp_username'],$admin['smtp_password'],$admin['smtp_auth']));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
    }
}

