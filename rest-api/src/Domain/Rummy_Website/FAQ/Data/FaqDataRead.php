<?php

namespace App\Domain\Rummy_Website\FAQ\Data;

/**
 * DTO.
 */
final class FaqDataRead
{
    public ?int $id = null;

    public ?string $title = null; 

    public ?string $answer = null;

    public ?string $status = null;

    public ?string $created_at = null;

}
