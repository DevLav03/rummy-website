<?php

namespace App\Domain\Master_Table\Master_Game_State\Data;

/**
 * DTO.
 */
final class StateDataRead
{
    public ?int $id = null;

    public ?string $state_code = null;

    public ?string $state_name = null;

    public ?string $user_alert_message = null;

    public ?string $status = null;

}
