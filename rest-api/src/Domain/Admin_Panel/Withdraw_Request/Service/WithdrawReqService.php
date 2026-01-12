<?php

namespace App\Domain\Admin_Panel\Withdraw_Request\Service;

//Data
use App\Domain\Admin_Panel\Withdraw_Request\Data\WithdrawReqData;
use App\Domain\Admin_Panel\Withdraw_Request\Data\WithdrawReqDataRead;

//Validator
use App\Domain\Admin_Panel\Withdraw_Request\Validator\WithdrawReqValidator;

//Service
use App\Service\Mail\MailService;
use App\Domain\Master_Table\Default_Mail\Service\DefaultMailService;

//Repository
use App\Domain\Admin_Panel\Withdraw_Request\Repository\WithdrawReqRepository;

use App\Domain\Admin_Panel\HaodaPayment\Service\PaymentGatewayService;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;


final class WithdrawReqService
{
    private WithdrawReqRepository $repository;
    private WithdrawReqValidator $withdrawReqValidator;
  
    private LoggerInterface $logger;
    private MailService $mailService;
    private DefaultMailService $service;
    private PaymentGatewayService $paymentGatewayService;


    public function __construct(PaymentGatewayService $paymentGatewayService, WithdrawReqRepository $repository, DefaultMailService $service,  LoggerFactory $loggerFactory, MailService $mailService, WithdrawReqValidator $withdrawReqValidator)
    {
        $this->repository = $repository;
        $this->withdrawReqValidator = $withdrawReqValidator;
        $this->mailService = $mailService;
        $this->service = $service; 
        $this->paymentGatewayService = $paymentGatewayService;
        $this->logger = $loggerFactory->addFileHandler('Admin_Panel/Withdraw_Request/Withdraw_Request.log')->createLogger();
    }

    //Get All Data
    public function getWithdrawsReq(array $data): WithdrawReqData
    {
        $this->withdrawReqValidator->validateData($data);

        $withdraws = $this->repository->getWithdrawsReq($data);

        $result = new WithdrawReqData();

        foreach ($withdraws as $withdrawRow) {
            $withdraw = new WithdrawReqDataRead();
            $withdraw->id = $withdrawRow['id'];
            $withdraw->user_id = $withdrawRow['user_id'];
            $withdraw->name = $withdrawRow['name'];
            $withdraw->username = $withdrawRow['username'];
            $withdraw->email = $withdrawRow['email'];
            $withdraw->phone_no = $withdrawRow['phone_no'];
            $withdraw->order_id = $withdrawRow['order_id'];
            $withdraw->transaction_id = $withdrawRow['transaction_id'];
            $withdraw->req_amount = $withdrawRow['req_amount'];
            $withdraw->req_date = $withdrawRow['req_date'];
            $withdraw->status = $withdrawRow['status'];
            $withdraw->status_change_by = $withdrawRow['status_change_by'];
            $withdraw->status_change_by_ip_address = $withdrawRow['status_change_by_ip_address'];
            $withdraw->status_change_date = $withdrawRow['status_change_date'];

            $result->withdraw_req[] = $withdraw;
        }

        return $result; 
    
    }

    public function getWithdrawsReqCount(array $data): int
    {
        $total = $this->repository->getWithdrawsReqCount($data);
        return  $total ; 
    }

    //block and unblock
    public function statusWithdrawReq(int $withdraw_id, int $status, int $admin_id, string $ip): int
    {

        $withdraw = $this->repository->statusWithdrawReq($withdraw_id, $status, $admin_id, $ip);
        $user = $this->repository->getUserDetail($withdraw_id); 

        //print_r($user); exit;

        $name = $user[0]['name'];
        $sender_mail = $user[0]['email'];
    
        $this->logger->info(sprintf('Status Change Successfully: %s', $withdraw_id));

        if($status == 1){
            
            $mail_details = $this->service->getMailTemplate('user_withdraw_success');
            $content = $mail_details['message'];

        }else{

            $mail_details = $this->service->getMailTemplate('user_withdraw_reject');
            $content = $mail_details['message'];

        }

        //Send Mail
        $mail = array($sender_mail);
        $sender_name = array($mail_details['name']); 
        $sub = $mail_details['subject'];
       
        $body_content = $this->named_printf($content, array('name'=>$name));

        $sample_file_attachment = '';
       
        $this->mailService->sendMail($mail, $sender_name, $sub, $body_content, $sample_file_attachment);

        return $withdraw;
    }

    function named_printf ($format_string, $values) {
        extract($values);
        $result = $format_string;
        eval('$result = "'.$format_string.'";');
        return $result;
    }


    //Debit
    public function checkAndSubmitWithdrawRequest($user_id, $amount){
        $res = $this->repository->checkAndUpdateWithdrawRequest($user_id, $amount);

        //print_r($res); exit;

        $order_info=$res[0];
        if($order_info['msg']=='success'){

            $order_id =$order_info['order_id'];
            $beneficiary_bank_name =$order_info['beneficiary_bank_name'];
            $beneficiary_account_ifsc =$order_info['beneficiary_account_ifsc'];
            $beneficiary_account_name =$order_info['beneficiary_account_name'];
            $beneficiary_account_number =$order_info['beneficiary_account_number'];
            $order_info['amount']=$amount;
            $order_info['user_id']=$user_id;

            $res = $this->paymentGatewayService->payoutRequest($order_info);
            
            $this->repository->submitWithdrawRequest($order_id, $res['str_res'], $res, $user_id, $amount);

            return $res;

        }else{

            return array('status'=>'failure','message'=>$order_info['msg']);

        }
    }    


    //Webhook
    public function webhookPayoutReq($data): array 
    {

        $status = $data['status'];
        $res = $data['data'];
        $res_str = json_encode($data);

        $withdraw = $this->repository->webhookPayoutReq($res, $res_str, $status);
        //print_r($withdraw); exit;
        return $withdraw[0];
    }  

    public function payoutStatusCheck($data): array 
    {

        $res = $this->paymentGatewayService->checkPayoutStatus($data);

        return $res;

    }  
    
    
}