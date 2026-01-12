<?php

namespace App\Factory;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

final class EmailFactory
{
    private string $host;
    private int $port;
    private string $username;
    private string $password;
    private string $from_name;
    private string $from_email;
    private bool $smtp_auth;
    private int $debug;


    
    public function __construct(array $emailSettings = [])
    {
        $this->host = (string)($emailSettings['host'] ?? '');
        $this->port = (int)($emailSettings['port'] ?? 25);
        $this->username = (string)($emailSettings['username'] ?? '');
        $this->password = (string)($emailSettings['password'] ?? '');
        $this->from_name = (string)($emailSettings['from_name'] ?? '');
        $this->from_email = (string)($emailSettings['from_email'] ?? '');
        $this->smtp_auth = (bool)($emailSettings['smtp_auth'] ?? false);
        $this->debug = (int)($emailSettings['debug'] ?? 0);
    }

    public function createMailer(): PHPMailer
    {
        $mail = new PHPMailer(true);
        
        // Server settings
        $mail->SMTPDebug = $this->debug;
        $mail->isSMTP();
        $mail->Host = $this->host;
        $mail->SMTPAuth =  $this->smtp_auth;
        $mail->Username =  $this->username;
        $mail->Password = $this->password;
        //$mail->SMTPSecure = $this->settings['password'];
        $mail->Port = $this->port;
        return $mail;
    }

}
