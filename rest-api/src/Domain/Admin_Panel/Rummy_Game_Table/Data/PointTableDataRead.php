<?php

namespace App\Domain\Admin_Panel\Rummy_Game_Table\Data;

/**
 * DTO.
 */
final class PointTableDataRead
{
    public ?int $total = null;
    
    public ?int $id = null;

    public ?string $game = null; 

    public ?string $game_type_id = null;

    public ?string $table_name = null;

    public ?string $table_no = null;

    public ?string $bet_value = null;

    public ?string $point_value = null;

    public ?string $sitting_capacity = null;

    public ?string $game_deck = null;

    public ?string $status = null;

    public ?string $table_status = null;

    public ?string $created_at = null;

}