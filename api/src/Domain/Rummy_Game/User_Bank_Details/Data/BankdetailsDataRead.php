<?php

namespace App\Domain\Rummy_Game\User_Bank_Details\Data;

/**
 * DTO.
 */
final class BankdetailsDataRead
{
    public ?int $id = null;

    public ?int $user_id = null; 

    public ?string $name = null; 

    public ?string $username = null; 

    public ?string $bank_name = null;

    public ?int $account_no = null;

    public ?string $ifsc_code = null;

    public ?string $date = null;

}
