<?php

namespace App\Domain\Rummy_Game\User_Bank_Details\Repository;

use PDO;

final class BankdetailsRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //Get All Data
    public function getBankDetails(): array
    {


        $query="SELECT u.name,u.username, udb.* FROM users_details_bank udb LEFT JOIN vw_users u ON udb.user_id = u.id";

        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();

        return $rows;
    }

    //Get One Data
    public function getUserBankDetails(array $data, int $userId): array
    {
        // print_r($userId); exit;

        $query="SELECT * FROM users_details_bank WHERE user_id = $userId";
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $rows;
    }


    //BankdetailsCount
    public function getBankDetailsCount(array $data): int
    {
        if(!empty($data['search_val'])){
            $searchString=" and (u.name like '%".$data['search_val']."%' or u.username like '%".$data['search_val']."%' or ubd.bank_name like '%".$data['search_val']."%') ";
        }else{
            $searchString="";
        }

        if(!empty($data['date'])){
            $query="SELECT count(ubd.id) as total FROM users as u LEFT JOIN users_details_bank ubd ON ubd.user_id = u.id WHERE DATE(ubd.created_at) = '".$data['date']."'".$searchString." ORDER BY created_at DESC limit ".$data['offset'].",".$data['limit'].";";           
        }else{
           
            $query="SELECT count(ubd.id) as total FROM users as u LEFT JOIN users_details_bank ubd ON ubd.user_id = u.id ".$searchString." ORDER BY ubd.created_at DESC limit ".$data['offset'].",".$data['limit'].";";
        }
     
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
    
        return $rows[0]['total'];
    }


    //Insert data
    public function insertBankDetails(array $data): int
    {           
        $statement =  $this->conn->prepare("INSERT INTO users_details_bank ( user_id, customer_name, bank_name, account_no, ifsc_code, ac_type) VALUES(?,?,?,?,?,?)");
        $statement->execute(array($data['user_id'], $data['customer_name'], $data['bank_name'], $data['account_no'], $data['ifsc_code'], $data['ac_type']));
        $id = $this->conn->lastInsertId();
		$statement->closeCursor();
        return $id;
    }


    //Update data
    public function updateBankDetails(int $bankdetailsid, array $data): int
    {
        $statement =  $this->conn->prepare("UPDATE users_details_bank SET user_id=?, customer_name=?, bank_name=?, account_no=?, ifsc_code=?, ac_type=? WHERE id=?");
        $statement->execute(array($data['user_id'], $data['customer_name'], $data['bank_name'],$data['account_no'],$data['ifsc_code'],$data['ac_type'], $bankdetailsid));     
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;
    }



}

