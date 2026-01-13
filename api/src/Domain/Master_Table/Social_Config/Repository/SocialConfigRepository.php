<?php

namespace App\Domain\Master_Table\Social_Config\Repository;

use PDO;

final class SocialConfigRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //get all data
    public function getSocialConfig(): array
    {   
        $statement = $this->conn->prepare("SELECT * FROM master_config_social");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows;
    }

    //update  data
    public function updateSocialConfig(array $admin): int
    {
     
        $statement =  $this->conn->prepare("update master_config_social set social_login_id=?, version=?, status=? ");
        $statement->execute(array($admin['social_login_id'], $admin['version'],$admin['status']));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
    }
}

