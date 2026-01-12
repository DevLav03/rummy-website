<?php

namespace App\Domain\Master_Table\Master_Game_Country\Repository;

use PDO;

final class CountryRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //get all data
    public function getCountry(): array
    {   
        $statement = $this->conn->prepare("SELECT * FROM master_rummy_country ");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows;
    }

    //get single data
    public function getOneCountry(int $countryId): array
    {
        $query="SELECT * FROM master_rummy_country   where id = ".$countryId;
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows; 
    }

    //insert data
    public function insertCountry(array $data): int
    {           
        $statement =  $this->conn->prepare("INSERT INTO master_rummy_country(country_name) VALUES(?)");
        $statement->execute(array($data['country_name']));
        $id = $this->conn->lastInsertId();
		$statement->closeCursor();
        return $id;
    }



    //Update data
    public function updateCountry(int $countryid, array $data): int
    {

        $statement =  $this->conn->prepare("UPDATE master_rummy_country SET country_code=?, country_name=? WHERE id=?");
        $statement->execute(array($data['country_code'], $data['country_name'], $countryid));      
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
    }


 
    //Delete data
    public function deleteCountry(int $countryId): int
    {
        $query="DELETE FROM master_rummy_country where id=".$countryId; 
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $count = $statement->rowCount();
		$statement->closeCursor();
        return $count;
    }

}

