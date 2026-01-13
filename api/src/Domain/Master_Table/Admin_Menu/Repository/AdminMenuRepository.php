<?php

namespace App\Domain\Master_Table\Admin_Menu\Repository;

use PDO;

final class AdminMenuRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //get all data
    public function getAllMenu(): array
    {  
        $sql = "SELECT * FROM `master_admin_roles_menu`"; 
        
        $statement = $this->conn->prepare($sql); 
        $statement->execute();
        $result = [];
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    
    public function getMenu(): array
    {  
        $sql = 'CALL get_menu_list()'; 
        $statement = $this->conn->prepare($sql); 
        $statement->execute();
        $result = [];
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }  

    //Update data
    public function updateMenu(int $menuId, array $data): int
    {
        $statement =  $this->conn->prepare("UPDATE master_admin_roles_menu SET menu_name=?, icons=?, order_id=?, parent_id=? where id=?");
        $statement->execute(array($data['menu_name'], $data['icons'], $data['order_id'],$data['parent_id'], $menuId));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
    }

    
    //Status Change
    public function menuStatus(int $menuId, int $status): int
    { 
        $statement =  $this->conn->prepare("UPDATE master_admin_roles_menu SET active=? where id=?");
        $statement->execute(array($status, $menuId));
        $count = $statement->rowCount();
        $statement->closeCursor();
        //print_r($count);exit;
        return $count;
    }  
 
   
}

