<?php

namespace App\Domain\Rummy_Game\Login\Data;

/**
 * DTO.
 */
final class LastLoginDataRead
{
    public ?int $id = null;

    public ?int $user_id = null; 

    public ?string $login_device = null;

    public ?string $country_name = null;

    public ?string $state_name = null;

    public ?string $city_name = null;

    public ?string $action = null;

    public ?string $location_ip = null;

    public ?string $created_at = null;

}