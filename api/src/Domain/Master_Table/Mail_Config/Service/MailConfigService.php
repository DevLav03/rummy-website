<?php

namespace App\Domain\Master_Table\Mail_Config\Service;

//Data
// use App\Domain\Master_Table\Mail_Config\Data\MailConfigData;
// use App\Domain\Master_Table\Mail_Config\Data\MailConfigDataRead;

//Validator
use App\Domain\Master_Table\Mail_Config\Validator\MailConfigUpdateValidator;

//Repository
use App\Domain\Master_Table\Mail_Config\Repository\MailConfigRepository;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;


final class MailConfigService
{
    private MailConfigRepository $repository;
    private MailConfigUpdateValidator $updateValidator;

    private LoggerInterface $logger;

    public function __construct(MailConfigRepository $repository, LoggerFactory $loggerFactory, MailConfigUpdateValidator $updateValidator)
    {
        $this->repository = $repository;
        $this->updateValidator = $updateValidator;

        $this->logger = $loggerFactory->addFileHandler('Master_Table/Mail_Config/mail_config.log')->createLogger();
    }

    //Get All Data
    public function getMailConfig(): array
    {
        $mails = $this->repository->getMailConfig();

        // $result = new MailConfigData();
     
        // foreach ($mails as $mailRow) {
        //     $mail = new MailConfigDataRead();
        //     $mail->send_mail = $mailRow['send_mail'];
        //     $mail->from_name = $mailRow['from_name'];
        //     $mail->smtp_host = $mailRow['smtp_host'];
        //     $mail->smtp_type  = $mailRow['smtp_type'];
        //     $mail->smtp_port  = $mailRow['smtp_port'];
        //     $mail->smtp_username  = $mailRow['smtp_username'];
        //     $mail->smtp_password  = $mailRow['smtp_password'];
        //     $mail->smtp_authentication  = $mailRow['smtp_authentication'];


        //     $result->mail_config[] = $mail;
        // }

        return $mails;
       
    }

    //Update Data
    public function updateMailConfig(array $data): int 
    { 
        $this->updateValidator->validateUpdateData($data);

        $mail = $this->repository->updateMailConfig($data); 

        $this->logger->info(sprintf('Updated successfully: %s', $data));

        return $mail;
    }

}
