<?php

namespace App\Domain\Admin_Panel\Promotion\Data;

/**
 * DTO.
 */
final class PromotionDataRead
{
    public ?int $id = null;

    public ?string $title = null; 

    public ?string $short_description = null;

    public ?string $description = null;

    public ?string $promotion_image = null;

    public ?string $status = null;

    public ?string $created_at = null;

}
