<?php

namespace App\Domain\Rummy_Game\Deposit\Repository;

use PDO;

final class DepositRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    public function getUserDetail($user_id){

        $statement = $this->conn->prepare("SELECT  u.id, u.name, u.email, u.phone_no FROM vw_users as u WHERE id =".$user_id);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows[0];

    }

    //Get Game Type
    public function createOrder($data, $users): int
    {
              
        $sql = 'CALL create_order(:in_customer_details,:in_order_amount, :in_order_currency, :in_order_expiry_time, :in_order_note, :in_notify_url, :in_payment_methods, :in_user_id, :in_remarks, :in_order_type)';
        $now = new \DateTime();
        $expiry_datetime= $now->format('Y-m-d H:i:s');
         
        $statement = $this->conn->prepare($sql);
        $statement->bindParam(':in_customer_details',  $data['customer_details'], PDO::PARAM_STR);
        $statement->bindParam(':in_order_amount', $data['order_amount'], PDO::PARAM_STR); 
        $statement->bindParam(':in_order_currency', $data['order_currency'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_order_expiry_time', $expiry_datetime, PDO::PARAM_STR);
        $statement->bindParam(':in_order_note', $data['order_note'], PDO::PARAM_STR,150);
        $statement->bindParam(':in_notify_url', $data['notify_url'], PDO::PARAM_STR,512);
        $statement->bindParam(':in_payment_methods', $data['payment_methods'], PDO::PARAM_STR,255);
        $statement->bindParam(':in_user_id', $data['user_id'], PDO::PARAM_INT,11);
        $statement->bindParam(':in_remarks', $data['remarks'], PDO::PARAM_STR,1000);
        $statement->bindParam(':in_order_type', $data['order_type'], PDO::PARAM_STR);

        $statement->execute();
        $result = [];
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        if($result[0]['res'] == 'success'){
            return 1;        
        }else{
            return 0;
        }

    }

      

}

?>