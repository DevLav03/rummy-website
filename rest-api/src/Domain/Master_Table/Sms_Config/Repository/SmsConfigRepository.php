<?php

namespace App\Domain\Master_Table\Sms_Config\Repository;

use PDO;

final class SmsConfigRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //get all data
    public function getSmsConfig(): array
    {   
        $statement = $this->conn->prepare("SELECT * FROM master_config_sms");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows;
    }

    //update  data
    public function updateSmsConfig(array $data): int
    {
     
        $statement =  $this->conn->prepare("UPDATE master_config_sms SET username=?, password=?,  sender_id=?, auth_key=?");
        $statement->execute(array($data['username'], $data['password'], $data['sender_id'], $data['auth_key']));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
    }
}

