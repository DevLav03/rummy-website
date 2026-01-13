<?php

namespace App\Domain\Rummy_Game\Kyc_Verify\Data;

/**
 * DTO.
 */
final class KycVerifyDataRead
{
    public ?int $id = null;

    public ?int $user_id = null; 

    public ?string $name = null;

    public ?string $username = null;

    public ?string $phone_no = null;

    public ?string $email = null;

    public ?string $kyc_verify_status = null;

    public ?string $phone_verify_status = null;  
       
    public ?string $pan_no = null;

    public ?string $pc_file = null;

    public ?string $pc_verify_status = null;

    public ?string $pc_requested_on = null;  
    
    public ?string $pc_verified_by = null;

    public ?string $pc_verify_by_ip_address = null;

    public ?string $pc_verified_on = null;

}
