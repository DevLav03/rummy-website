<?php

namespace App\Domain\Admin_Panel\Withdraw_Request\Repository;

use PDO;

final class WithdrawReqRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    public function checkAndUpdateWithdrawRequest($user_id, $amount)
    {
        $sql = 'CALL send_withdraw_request(:in_user_id,:in_amount)';
        $statement = $this->conn->prepare($sql);
        $statement->bindParam(':in_user_id', $user_id, PDO::PARAM_INT);
        $statement->bindParam(':in_amount', $amount);
        $statement->execute();
        $result =  $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    //Debit
    public function submitWithdrawRequest(int $order_id, $res_str, $res, $user_id, $amount): array
    {   

        $status = $res['message']['status'];
        $payout_id = $res['message']['payout_id'];

        $sql = 'CALL send_withdraw_request_response(:in_order_id,:in_order_status,:in_payout_id, :in_res_str, :in_user_id, :in_amount)';
        $statement = $this->conn->prepare($sql);
        $statement->bindParam(':in_order_id', $order_id, PDO::PARAM_INT);
        $statement->bindParam(':in_order_status', $status);
        $statement->bindParam(':in_payout_id', $payout_id);
        $statement->bindParam(':in_res_str', $res_str);
        $statement->bindParam(':in_user_id', $user_id);
        $statement->bindParam(':in_amount', $amount);
        $statement->execute();
        $result =  $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;

    }

    //Webhook
    public function webhookPayoutReq($res, $res_str, $status): array
    {   

        $sql = 'CALL send_withdraw_request_success_response(:in_amount,:in_remarks,:in_created_at, :in_payment_mode, :in_transfer_date, :in_beneficiary_bank_name, :in_payout_id, :in_beneficiary_account_ifsc, :in_beneficiary_account_name, :in_beneficiary_account_number, :in_beneficiary_upi_handle, :in_utr, :in_status, :in_res_str)';

        $statement = $this->conn->prepare($sql);
        
        $statement->bindParam(':in_amount', $res['amount']);
        $statement->bindParam(':in_remarks', $res['remarks']);
        $statement->bindParam(':in_created_at', $res['created_at']);
        $statement->bindParam(':in_payment_mode', $res['payment_mode']);
        $statement->bindParam(':in_transfer_date', $res['transfer_date']);
        $statement->bindParam(':in_beneficiary_bank_name', $res['beneficiary_bank_name']);
        $statement->bindParam(':in_payout_id', $res['payout_id']);
        $statement->bindParam(':in_beneficiary_account_ifsc', $res['beneficiary_account_ifsc']);
        $statement->bindParam(':in_beneficiary_account_name', $res['beneficiary_account_name']);
        $statement->bindParam(':in_beneficiary_account_number', $res['beneficiary_account_number']);
        $statement->bindParam(':in_beneficiary_upi_handle', $res['beneficiary_upi_handle']);
        $statement->bindParam(':in_utr', $res['UTR']);
        $statement->bindParam(':in_status', $status);
        $statement->bindParam(':in_res_str', $res_str);
 
        $statement->execute();
   
        //print_r($statement->debugDumpParams()); exit;
        $result =  $statement->fetchAll(PDO::FETCH_ASSOC);

        //print_r($result); exit;

        return $result;

    }

    //get all data
    public function getWithdrawsReq(array $data): array
    {
        if(!empty($data['search_val'])){
            $searchString=" and (u.name like '%".$data['search_val']."%' OR u.username like '%".$data['search_val']."%' OR u.email like '%".$data['search_val']."%' OR u.phone_no like '%".$data['search_val']."%' ) ";
        }else{
            $searchString="";
        }
        $query="SELECT u.name, u.username, u.email, u.phone_no, uwc.* FROM users_cash_chips_withdraw_request as uwc  INNER JOIN vw_users as u ON u.id = uwc.user_id INNER JOIN users_details_profile as udp ON udp.id = u.id WHERE DATE(uwc.req_date) BETWEEN '".$data['start_date']."' AND '" .$data['end_date']. "' ".$searchString." ORDER BY uwc.req_date DESC limit ".$data['offset'].",".$data['limit'].";";
    
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
   
        return $rows;
    }

    public function getWithdrawsReqCount(array $data): int
    {
        if(!empty($data['search_val'])){
            $searchString=" and (u.name like '%".$data['search_val']."%' OR u.username like '%".$data['search_val']."%' OR u.email like '%".$data['search_val']."%' OR u.phone_no like '%".$data['search_val']."%') ";
        }else{
            $searchString="";
        }
        
        $query="SELECT count(u.id) as total  FROM users_cash_chips_withdraw_request as uwc  INNER JOIN users as u ON u.id = uwc.user_id  WHERE DATE(uwc.req_date) BETWEEN '".$data['start_date']."' AND '" .$data['end_date']."' ".$searchString.";";
        $statement = $this->conn->prepare($query); 
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
    
        return $rows[0]['total'];
    }

    //block and unblock 
    public function statusWithdrawReq(int $withdraw_id, int $status, int $admin_id, string $ip): int
    { 

        $now = new \DateTime();        
        $current_datetime= $now->format('Y-m-d H:i:s'); 

        $statement =  $this->conn->prepare("UPDATE users_cash_chips_withdraw_request SET status=?, status_change_by=?, status_change_by_ip_address=?, status_change_date=? WHERE id=?");
        $statement->execute(array($status, $admin_id, $ip, $current_datetime, $withdraw_id));
        $count = $statement->rowCount();
        $statement->closeCursor();
    
        return $count;
    }  

    public function getUserDetail(int $id): array
    {
        $query="SELECT user_id FROM users_cash_chips_withdraw_request WHERE id =" .$id;       
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC); 
        $statement->closeCursor();

        $user = $this->getUser($rows);
   
        return $user;
    }

    public function getUser(array $user): array
    {

        $query="SELECT u.name,u.username, u.email FROM vw_users u WHERE id = " .$user[0]['user_id']; 
        $statement = $this->conn->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
   
        return $rows;
        
    }

    

   
}
