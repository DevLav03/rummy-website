<?php

namespace App\Domain\Admin_Panel\Withdraw_Request\Data;

/**
 * DTO.
 */
final class WithdrawReqDataRead
{

    public ?int $id = null;

    public ?int $user_id = null;

    public ?String $name = null;

    public ?string $username = null; 

    public ?string $email = null;

    public ?int $order_id = null;

    public ?string $transaction_id = null;

    public ?int $phone_no = null;

    public ?string $req_amount = null;

    public ?string $req_date = null;

    public ?int $status = null;

    public ?int $status_change_by = null;

    public ?string $status_change_by_ip_address = null;

    public ?string $status_change_date = null;

}
