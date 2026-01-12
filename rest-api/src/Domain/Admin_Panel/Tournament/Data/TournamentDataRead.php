<?php

namespace App\Domain\Admin_Panel\Tournament\Data;

/**
 * DTO.
 */
final class TournamentDataRead
{
    public ?int $id = null;

    public ?string $title = null;

    public ?string $price = null;

    public ?string $start_date = null;

    public ?string $start_time = null;

    public ?string $registration_start_date = null;

    public ?string $registration_start_time = null;

    public ?string $registration_end_date = null;

    public ?string $registration_end_time = null;

    public ?string $position = null;

    public ?string $entry_fees = null;

    public ?string $paid_amount = null;

    public ?string $no_of_players = null;

    public ?string $description = null;

    public ?string $status = null;
    


}