<?php

namespace App\Domain\Master_Table\Master_Game_Match_Types\Data;

/**
 * DTO.
 */
final class MatchTypeDataRead
{
    public ?int $id = null;

    public ?string $name = null;

    public ?string $description = null;

    public ?string $active = null;

    public ?string $created_at = null;
}
