<?php

namespace App\Domain\Rummy_Game\Gameroom_PrivateTable\Repository;

use PDO;

final class GameRoomPrivateTableRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

  
    //Insert data
    public function insertPrivateTable(array $data, int $user_id): string
    {           
        
        $statement =  $this->conn->prepare("INSERT INTO game_room_club (game_table_id, user_id) VALUES(?,?)");
        $statement->execute(array($data['game_table_id'],$user_id));
        $id = $this->conn->lastInsertId();
        $statement->closeCursor();

        $code = $this->updateCode($id);

        return $code;
    }

    public function updateCode(int $id): string
    {

        $code = "7SBAR0".$id;
        $statement =  $this->conn->prepare("UPDATE game_room_club SET code=? WHERE id=?");
        $statement->execute(array($code, $id));
        $statement->closeCursor();
        return $code;

    }

    //Enter Private Table
    public function enterPrivateTable(array $data): string
    {
        $sql = "SELECT count(id) as counts FROM game_room_club WHERE code = '".$data['code']."' and status = '0'";
        $statement = $this->conn->prepare($sql);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        $count = $rows[0]['counts'];
        return $count; 
    }

 
   

   

}
