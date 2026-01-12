<?php

namespace App\Domain\Master_Table\Master_Software\Repository;

use PDO;

final class SoftwareRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //Get Android User 
    public function getAndroid(): array
    {   
        $statement = $this->conn->prepare("SELECT * FROM master_software WHERE app_type = 'android' ORDER BY created_at desc LIMIT 1");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows;
    }


    //Get IOS User
    public function getIos(): array
    {   
        $statement = $this->conn->prepare("SELECT * FROM master_software WHERE app_type = 'ios' ORDER BY created_at desc LIMIT 1");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows;
    }
 

    //Version Insert Validation
    public function versionInsertValid(string $version): int
    {
        $query="SELECT count(id) as counts FROM master_software WHERE app_version = '" .$version . "'";
        //print_r($query); exit;
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        $count = $rows[0]['counts'];
        return $count; 
    }

    //Insert data
    public function insertSoftwareversion(array $data): int
    {           
        $statement =  $this->conn->prepare("INSERT INTO master_software (app_type, app_version) VALUES(?,?)");
        $statement->execute(array($data['app_type'],$data['app_version']));
        $id = $this->conn->lastInsertId();
		$statement->closeCursor();
        return $id;
    }

}
