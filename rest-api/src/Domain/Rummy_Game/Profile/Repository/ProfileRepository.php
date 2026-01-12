<?php

namespace App\Domain\Rummy_Game\Profile\Repository;

use PDO;

final class ProfileRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //update profile
    public function updateUserProfile(int $userId, array $user): int
    {
        $statement =  $this->conn->prepare("UPDATE users_details_profile  SET name=?, gender=?, dateofbirth=?  WHERE id=?");
        $statement->execute(array($user['name'],$user['gender'],$user['dateofbirth'],$userId));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;

    }
 
    //Change Password
    public function getCurrentPassword(int $userId, string $currentPassword): array
    {
        $query="SELECT * FROM users where id=".$userId ." and password='".$currentPassword."'"; 
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $rows; 
    }

    public function changePassword(int $userId, string $password): int
    {
        $statement =  $this->conn->prepare("UPDATE users SET password=?  WHERE id=?");
        $statement->execute(array($password, $userId));
        $statement->closeCursor();
        return $userId;
    }

    
    //Insert
    public function uploadProfileImage(int $userId, $filename ): int
    {
        $statement =  $this->conn->prepare("UPDATE  users_details_profile SET profile_image=? WHERE user_id=? ");
        $statement->execute(array($filename, $userId ));
        $id = $this->conn->lastInsertId();
        $statement->closeCursor();
        return $id;
    }
 
 
}