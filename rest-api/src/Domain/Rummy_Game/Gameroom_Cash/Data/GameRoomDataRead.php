<?php

namespace App\Domain\Rummy_Game\Gameroom_Cash\Data;

/**
 * DTO.
 */
final class GameRoomDataRead
{
    public ?int $id = null;

    public ?int $game_table_id = null;

    public ?string $joker_type = null;

    public ?int $deck = null;

    public ?int $total_bet = null;

    public ?int $active = null;

    public ?string $created_at = null;

    

}