<?php

namespace App\Domain\Master_Table\Admin_Menu\Data;

/**
 * DTO.
 */
final class AdminMenuDataRead
{
    public ?int $id = null;

    public ?string $menu_name = null; 

    public ?string $menu_key = null;

    public ?string $menu_link = null;

    public ?string $parent_id = null;

    public ?string $order_id = null;

    public ?string $status = null;

 

}
