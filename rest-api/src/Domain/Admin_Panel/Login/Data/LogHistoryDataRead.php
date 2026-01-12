<?php

namespace App\Domain\Admin_Panel\Login\Data;

/**
 * DTO.
 */
final class LogHistoryDataRead
{
    public ?int $admin_id = null;

    public ?string $login_device = null;

    public ?string $action = null;

    public ?string $location_ip = null;

    public ?string $created_at = null;

}