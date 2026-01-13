<?php

namespace App\Domain\Master_Table\Default_Sms\Service;

//Data
use App\Domain\Master_Table\Default_Sms\Data\DefaultSmsData;
use App\Domain\Master_Table\Default_Sms\Data\DefaultSmsDataRead;

//Repository
use App\Domain\Master_Table\Default_Sms\Repository\DefaultSmsRepository;

//Validator
use App\Domain\Master_Table\Default_Sms\Validator\DefaultSmsUpdateValidator;
use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;

final class DefaultSmsService
{
    private DefaultSmsRepository $repository;
    // private WelcomeBonusCreateValidator $createValidator;
    private DefaultSmsUpdateValidator $updateValidator;

    

    private LoggerInterface $logger;

    public function __construct(DefaultSmsRepository $repository, DefaultSmsUpdateValidator $updateValidator,  LoggerFactory $loggerFactory) // WelcomeBonusCreateValidator $createValidator,
    {
        $this->repository = $repository;
        // $this->createValidator = $createValidator;
        $this->updateValidator = $updateValidator;

        $this->logger = $loggerFactory->addFileHandler('Master_Table/Default_Sms/DefaultSms.log')->createLogger();
    }

    //Get all defaultsms
    public function getAllDefaultSms(): array
    {
        $default = $this->repository->getAllDefaultSms($data);
    
        return $default;
        
    }


    //Get one defaultsms
    public function getOneDefaultSms(int $defaultsms_id)
    {
        $default = $this->repository->getOneDefaultSms($defaultsms_id);
    
        return $default;
        
    }


    //Update Data
    public function updateDefaultSms(int $defaultsms_id, array $data): int
    { 
        $this->updateValidator->validateUpdateData($data);

        $default = $this->repository->updateDefaultSms($defaultsms_id, $data);

        // $mail_details = $this->repository->getDefaultmail($defaultmailId);

        $this->logger->info(sprintf('DefaultSms updated successfully: %s', $defaultsmsId));

        return $default;
    }



}