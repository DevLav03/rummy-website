<?php

namespace App\Service\Mail;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
//use App\Domain\Mail\Repository\MailRepository;

use App\Domain\Master_Table\Mail_Config\Service\MailConfigService;

use App\Factory\EmailFactory;

final class MailService
{
   
    private LoggerInterface $logger;
    private EmailFactory $emailFactory;
    private MailConfigService $mailConfigService;
    private $emailConfigExists = true;
    //private MailRepository $repository;

    // public function __construct(EmailFactory $emailFactory)
    // {
    //     $this->emailFactory = $emailFactory;
    //    // $this->repository = $repository;       
    // }

    public function __construct(MailConfigService $mailConfigService)
    {

        $this->mailConfigService = $mailConfigService;

        $emailData = $this->mailConfigService->getMailConfig();

        $emailSettings = $emailData[0];

        //print_r( $emailSettings); exit;

        if(count($emailSettings) > 0){

            $this->emailConfigExists = true;

            $this->host = (string)($emailSettings['smtp_host'] ?? '');
            $this->port = (int)($emailSettings['smtp_port'] ?? 25);
            $this->username = (string)($emailSettings['smtp_username'] ?? '');
            $this->password = (string)($emailSettings['smtp_password'] ?? '');
            $this->from_name = (string)($emailSettings['from_name'] ?? '');
            $this->from_email = (string)($emailSettings['from_email'] ?? '');
            $this->smtp_type = (string)($emailSettings['smtp_type'] ?? '');
            $this->smtp_auth = (bool)($emailSettings['smtp_auth'] ?? false);
            $this->debug = (int)($emailSettings['debug'] ?? 0);

        }else{

            $this->emailConfigExists = false;

        }

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
    
    public function sendMail(array $to_email, array $to_name,string $subject, string $body, string $attachment)
    {
        try {
            $mail=$this->createMailer();
            $mail->SMTPSecure = 'tls';
            $mail->Mailer = "smtp";
            $mail->addAddress($to_email[0], $to_name[0]);       
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            if(trim($attachment) != ''){
                $mail->addAttachment($attachment);
            }
            $mail->send();
            //echo 'success';
        } catch (Exception $e) {
           //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
            
    }


   
}
