<?php

namespace App\Domain\Master_Table\Ip_Config\Data;

/**
 * DTO.
 */
final class IpConfigDataRead
{
    //public ?int $id = null;

    public ?string $game_ip_address = null;

    public ?string $game_port_number = null;

    public ?string $game_domain_name = null;

    public ?string $tourney_ip_address = null;

    public ?string $tourney_port_number = null;
    
    public ?string $tourney_domain_name = null;


}
