<?php

namespace App\Domain\Master_Table\Sms_Config\Service;

//Data
use App\Domain\Master_Table\Sms_Config\Data\SmsConfigData;
use App\Domain\Master_Table\Sms_Config\Data\SmsConfigDataRead;

//Validator
use App\Domain\Master_Table\Sms_Config\Validator\SmsConfigUpdateValidator;

//Repository
use App\Domain\Master_Table\Sms_Config\Repository\SmsConfigRepository;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;


final class SmsConfigService
{
    private SmsConfigRepository $repository;
    private SmsConfigUpdateValidator $updateValidator ;

    private LoggerInterface $logger;

    public function __construct(SmsConfigRepository $repository, LoggerFactory $loggerFactory, SmsConfigUpdateValidator $updateValidator)
    {
        $this->repository = $repository;
        $this->updateValidator = $updateValidator;

        $this->logger = $loggerFactory->addFileHandler('Master_Table/Sms_Config/sms_config.log')->createLogger();
    }

    //Get All Data
    public function getSmsConfig(): SmsConfigData
    {
        $sms = $this->repository->getSmsConfig();

        $result = new SmsConfigData();
     
        foreach ($sms as $smsRow) {
            $sms = new SmsConfigDataRead();
            $sms->username = $smsRow['username'];
            $sms->password = $smsRow['password'];
            $sms->sender_id = $smsRow['sender_id'];
            $sms->auth_key = $smsRow['auth_key'];
           

            $result->sms_config[] = $sms;
        }

        return $result;
       
    }

    //Update Data
    public function updateSmsConfig(array $data): int 
    { 
        $this->updateValidator->validateUpdateData($data);

        $sms = $this->repository->updateSmsConfig($data); 

        $this->logger->info(sprintf('Updated successfully: %s', $data));

        return $sms;
    }

}
