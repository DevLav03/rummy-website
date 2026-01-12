<?php

namespace App\Domain\Rummy_Game\Leaderboard\Repository;

use PDO;

final class LeaderboardRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //leaderboard top 15 players
    public function getTop15Players(): array
    {   
        $statement = $this->conn->prepare("SELECT u.id, udp.name, u.username, up.total_point FROM users_point up LEFT JOIN users u ON u.id = up.user_id LEFT JOIN users_details_profile udp ON udp.user_id = u.id ORDER BY total_point DESC LIMIT 15");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows;
    }

    //refer & earn top 15
    public function referearnTop15Players(): array
    {   
        $statement = $this->conn->prepare("SELECT u.id, udp.name, u.username, urp.total_point FROM users_refer_earn_point urp LEFT JOIN users u ON u.id = urp.user_id LEFT JOIN users_details_profile udp ON udp.user_id = u.id ORDER BY total_point DESC LIMIT 15");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows;
    }


}

