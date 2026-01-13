<?php

namespace App\Domain\Admin_Panel\Coupon\Repository;

use PDO;

final class CouponRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //Get all data
    public function getCoupon(): array
    {   
        $statement = $this->conn->prepare("SELECT * FROM game_coupons ");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows;
    }

    //Get single data
    public function getOneCoupon(int $couponId): array
    {
        $query="SELECT * FROM game_coupons where id = ".$couponId;
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows; 
    }

    //Insert data
    public function insertCoupon(array $data): int
    {           
        $statement =  $this->conn->prepare("INSERT INTO game_coupons ( coupon_title, coupon_code, valid_from_date, valid_to_date, bonus_type, bonus_value,max_price, reusable) VALUES(?,?,?,?,?,?,?,?)");
        $statement->execute(array($data['coupon_title'],$data['coupon_code'],$data['valid_from_date'], $data['valid_to_date'], $data['bonus_type'], $data['bonus_value'],$data['max_price'],$data['reusable']));
        $id = $this->conn->lastInsertId();
		$statement->closeCursor();
        return $id;
    }


    //Update data
    public function updateCoupon(int $couponId, array $data): int
    {

      
        $statement =  $this->conn->prepare("UPDATE game_coupons SET coupon_title=?, coupon_code=?, valid_from_date=?, valid_to_date=?, bonus_type=?, bonus_value=?, max_price=?, reusable=? WHERE id=?");
        $statement->execute(array($data['coupon_title'], $data['coupon_code'],$data['valid_from_date'],$data['valid_to_date'],$data['bonus_type'],$data['bonus_value'],$data['max_price'],$data['reusable'], $couponId));
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
    }

    //delete data
    public function deleteCoupon(int $couponId): int
    {
        $query="DELETE FROM game_coupons where id=".$couponId; 
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $count = $statement->rowCount();
		$statement->closeCursor();
        return $count;
    }

}

