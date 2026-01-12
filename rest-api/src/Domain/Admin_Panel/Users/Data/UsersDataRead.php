<?php

namespace App\Domain\Admin_Panel\Users\Data;

/**
 * DTO.
 */
final class UsersDataRead
{
    public ?int $id = null;

    public ?string $name = null;

    public ?string $username = null;

    public ?string $email = null;

    public ?string $phone_no = null;

    public ?string $active = null;

    public ?string $last_action_time = null;

    public ?string $premium_flag = null;

    public ?string $online_status = null;

    public ?string $profile_image = null;
     
}
