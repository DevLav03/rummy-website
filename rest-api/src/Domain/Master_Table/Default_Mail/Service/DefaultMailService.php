<?php

namespace App\Domain\Master_Table\Default_Mail\Service;

//Data
use App\Domain\Master_Table\Default_Mail\Data\DefaultMailData;
use App\Domain\Master_Table\Default_Mail\Data\DefaultMailDataRead;

//Repository
use App\Domain\Master_Table\Default_Mail\Repository\DefaultMailRepository;

//Validator
// use App\Domain\Master_Table\Default_Mail\Validator\WelcomeBonusCreateValidator;
use App\Domain\Master_Table\Default_Mail\Validator\DefaultMailUpdateValidator;
use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;

final class DefaultMailService
{
    private DefaultMailRepository $repository;
    // private WelcomeBonusCreateValidator $createValidator;
    private DefaultMailUpdateValidator $updateValidator;

    

    private LoggerInterface $logger;

    public function __construct(DefaultMailRepository $repository, DefaultMailUpdateValidator $updateValidator,  LoggerFactory $loggerFactory) // WelcomeBonusCreateValidator $createValidator,
    {
        $this->repository = $repository;
        // $this->createValidator = $createValidator;
        $this->updateValidator = $updateValidator;

        $this->logger = $loggerFactory->addFileHandler('Master_Table/Default_Mail/DefaultMail.log')->createLogger();
    }

    //Get all defaultmail
    public function getAllDefaultMail(): array
    {
        $default = $this->repository->getAllDefaultMail($data);
    
        return $default;
        
    }


    //Get one defaultmail
    public function getOneDefaultMail(int $defaultmail_id)
    {
        $default = $this->repository->getOneDefaultMail($defaultmail_id);
    
        return $default;
        
    }


    //Update Data
    public function updateDefaultMail(int $defaultmailId, array $data): int
    { 
        $this->updateValidator->validateUpdateData($data);

        $default = $this->repository->updateDefaultMail($defaultmailId, $data);

        // $mail_details = $this->repository->getDefaultmail($defaultmailId);

        $this->logger->info(sprintf('DefaultMail updated successfully: %s', $defaultmailId));

        return $default;
    }


    public function getMailTemplate(string $mail_type): array
    { 
        $default = $this->repository->getMailTemplate($mail_type);

        return $default;
    }


   

}