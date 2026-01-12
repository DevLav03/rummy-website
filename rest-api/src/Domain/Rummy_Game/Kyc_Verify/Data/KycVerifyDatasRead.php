<?php

namespace App\Domain\Rummy_Game\Kyc_Verify\Data;

/**
 * DTO.
 */
final class KycVerifyDatasRead
{
    public ?int $id = null;

    public ?int $user_id = null; 

    public ?string $kyc_verify_status = null;

    public ?string $pan_no = null;

    public ?string $pc_file = null;

    public ?string $created_at = null;

}
