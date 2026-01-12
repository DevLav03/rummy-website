<?php

namespace App\Domain\Rummy_Game\Point_Claim_Bonus\Repository;

use PDO;

final class PointClaimBonusRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    public function PointClaimBonus(array $data, int $user_id): array
    {           
        //print_r($user_id); exit;
        $sql = 'CALL game_point_to_claim_bonus(:in_bonus,:in_user_id)';
        $statement = $this->conn->prepare($sql);
        $statement->bindParam(':in_bonus', $data['bonus'], PDO::PARAM_INT,11);
        $statement->bindParam(':in_user_id', $user_id, PDO::PARAM_INT,20);
        $statement->execute();
        $result = [];
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        //print_r($result); exit; 

        return $result;
    }

   


}

