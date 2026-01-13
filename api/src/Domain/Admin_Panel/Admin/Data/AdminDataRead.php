<?php

namespace App\Domain\Admin_Panel\Admin\Data;

/**
 * DTO.
 */
final class AdminDataRead
{
    public ?int $id = null;

    public ?string $name = null; 

    public ?string $username = null;

    public ?string $email = null;

    public ?string $phone_no = null;

    public ?string $role_id = null;

    public ?string $role_name = null;

    public ?string $role_type = null;

    public ?string $active = null;

    public ?string $ip_restrict = null;

    public ?string $time_in = null;

    public ?string $time_out = null;


    //public ?string $created_at = null;

}
