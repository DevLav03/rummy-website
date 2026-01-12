<?php

namespace App\Domain\Admin_Panel\Users\Data;

/**
 * DTO.
 */
final class UserLogDataRead
{
    public ?int $id = null;

    public ?int $user_id = null;

    public ?string $login_device = null;

    public ?string $user_country_name = null;

    public ?string $user_state_name = null;

    public ?string $user_city_name = null;

    public ?string $location_ip = null;

    public ?string $action = null;

    public ?string $created_at = null;

}
