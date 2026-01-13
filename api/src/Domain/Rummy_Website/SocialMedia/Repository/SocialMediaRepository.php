<?php

namespace App\Domain\Rummy_Website\SocialMedia\Repository;

use PDO;

final class SocialMediaRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //get all data
    public function getSocialMedia(): array
    {   
        $statement = $this->conn->prepare("SELECT * FROM ws_social_media");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows;
    }

    //Update data
    public function updateSocialMedia(array $data): int
    {
        $statement =  $this->conn->prepare("UPDATE ws_social_media SET facebook=?, google=?, playstore=?, android=?, ios=? ");
        $statement->execute(array($data['facebook'], $data['google'], $data['playstore'],$data['android'],$data['ios']));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
    }
}
