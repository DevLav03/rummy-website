<?php

namespace App\Domain\Master_Table\Mail_Config\Data;

/**
 * DTO.
 */
final class MailConfigDataRead
{
    public ?string $send_mail = null;

    public ?string $from_name = null; 

    public ?string $smtp_host = null;

    public ?string $smtp_type = null;

    public ?string $smtp_port = null;

    public ?string $smtp_username = null;

    public ?string $smtp_password = null;
    
    public ?string $smtp_authentication = null;


}
