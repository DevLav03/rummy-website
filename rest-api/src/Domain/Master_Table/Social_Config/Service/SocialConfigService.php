<?php

namespace App\Domain\Master_Table\Social_Config\Service;

//Data
use App\Domain\Master_Table\Social_Config\Data\SocialConfigData;
use App\Domain\Master_Table\Social_Config\Data\SocialConfigDataRead;

//Validator
use App\Domain\Master_Table\Social_Config\Validator\SocialConfigUpdateValidator;

//Repository
use App\Domain\Master_Table\Social_Config\Repository\SocialConfigRepository;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;


final class SocialConfigService
{
    private SocialConfigRepository $repository;
    private SocialConfigUpdateValidator $updateValidator;

    private LoggerInterface $logger;

    public function __construct(SocialConfigRepository $repository, LoggerFactory $loggerFactory, SocialConfigUpdateValidator $updateValidator)
    {
        $this->repository = $repository;
        $this->updateValidator = $updateValidator;

        $this->logger = $loggerFactory->addFileHandler('Master_Table/Social_Config/social_config.log')->createLogger();
    }

    //Get All Data
    public function getSocialConfig(): SocialConfigData
    {
        $social = $this->repository->getSocialConfig();

        $result = new SocialConfigData();
     
        foreach ($social as $socialRow) {
            $social = new SocialConfigDataRead();
            $social->id = $socialRow['id'];
            $social->social_login_id = $socialRow['social_login_id'];
            $social->version = $socialRow['version'];
            $social->status  = $socialRow['status'];
           


            $result->social_config[] = $social;
        }

        return $result;
       
    }

    //Update Data
    public function updateSocialConfig(array $data): int 
    { 
        $this->updateValidator->validateUpdateData($data);

        $social = $this->repository->updateSocialConfig($data); 

        $this->logger->info(sprintf('Updated successfully: %s', $data));

        return $social;
    }

}
