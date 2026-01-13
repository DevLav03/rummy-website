<?php

namespace App\Domain\Master_Table\Rummy_Format_Types\Repository;

use PDO;

final class RummyFormatTypesRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //get all data
    public function getRummyFormatTypes(int $format_id): array
    {   
        $statement = $this->conn->prepare("SELECT ft.format_id, ft.name, ft.discription, ft.format_type_id, f.name as format_name, ft.active, ft.createtime FROM master_rummy_format_types ft LEFT JOIN master_rummy_format f ON f.format_id = ft.format_type_id WHERE f.format_id = $format_id");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows;
    }

    //get active data
    public function getActiveRummyFormatTypes(): array
    {   
        $statement = $this->conn->prepare("SELECT ft.format_id, ft.name, ft.discription, ft.format_type_id, f.name as format_name, ft.active, ft.createtime FROM master_rummy_format_types ft LEFT JOIN master_rummy_format f ON f.format_id = ft.format_type_id WHERE ft.active = '1'");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $rows;
    }


    //insert data
    public function insertRummyFormatTypes(array $data): int
    {           
        $statement =  $this->conn->prepare("INSERT INTO master_rummy_format_types (name, discription, format_id) VALUES(?,?,?)");
        $statement->execute(array($data['name'],$data['description'],$data['format_id']));
        $id = $this->conn->lastInsertId();
        $statement->closeCursor();
        return $id;
    }

    //Update data
    public function updateRummyFormatTypes(int $formatTypeId, array $data): int
    {
        $statement =  $this->conn->prepare("UPDATE master_rummy_format_types SET name=?, discription=?, format_id=? WHERE format_type_id=?");
        $statement->execute(array($data['name'], $data['description'], $data['format_id'], $formatTypeId));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
    }

    //Status Change
    public function rummyFormatTypeStatus(int $formatTypeId, int $status): int
    {
        $statement =  $this->conn->prepare("UPDATE master_rummy_format_types SET active=? WHERE format_type_id=?");
        $statement->execute(array($status, $formatTypeId));
        $count = $statement->rowCount();
        $statement->closeCursor();

        return $count;    
    }

    //Delete data
    public function deleteRummyFormatTypes(int $formatTypeId): int
    {
        $query="DELETE FROM master_rummy_format_types where format_type_id=".$formatTypeId; 
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
    }

}
