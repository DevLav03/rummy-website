<?php

namespace App\Domain\Rummy_Website\News\Data;

/**
 * DTO.
 */
final class NewsDataRead
{
    public ?int $id = null;

    public ?string $title = null; 

    public ?string $image = null;

    public ?string $sub_description = null; 

    public ?string $content = null; 

    public ?string $link = null; 

    public ?string $status = null;

    public ?string $created_at = null;

}
