<?php

namespace App\Domain\Master_Table\Ip_Config\Repository;

use PDO;

final class IpConfigRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //get all data
    public function getIpConfig(): array
    {   
        $statement = $this->conn->prepare("SELECT * FROM master_config_ip");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows;
    }

    //update  data
    public function updateIpConfig(array $admin): int
    {
     
        $statement =  $this->conn->prepare("update master_config_ip set game_ip_address=?, game_port_number=?, game_domain_name=?, tourney_ip_address=?, tourney_port_number=?, tourney_domain_name	=? ");
        $statement->execute(array($admin['game_ip_address'], $admin['game_port_number'],$admin['game_domain_name'],$admin['tourney_ip_address'],$admin['tourney_port_number'],$admin['tourney_domain_name']));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
    }
}

