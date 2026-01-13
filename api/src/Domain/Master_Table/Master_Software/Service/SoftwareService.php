<?php

namespace App\Domain\Master_Table\Master_Software\Service;

//Data
use App\Domain\Master_Table\Master_Software\Data\SoftwareData;
use App\Domain\Master_Table\Master_Software\Data\SoftwareDataRead;

use App\Domain\Master_Table\Master_Software\Data\SoftwareIosData;
use App\Domain\Master_Table\Master_Software\Data\SoftwareIosDataRead;

//Repository
use App\Domain\Master_Table\Master_Software\Repository\SoftwareRepository;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;

final class SoftwareService
{
    private SoftwareRepository $repository;
    private LoggerInterface $logger;

    public function __construct(SoftwareRepository $repository, LoggerFactory $loggerFactory)
    {
        $this->repository = $repository;
        $this->logger = $loggerFactory->addFileHandler('Master_Table/Master_Software/Master_software.log')->createLogger();
    }

    //Get All Data
    public function getAndroid(): SoftwareData
    {
        $android = $this->repository->getAndroid();

        $result = new SoftwareData();

        foreach ($android as $androidRow) {
            $android = new SoftwareDataRead();
            $android->id  = $androidRow['id'];
            $android->app_type = $androidRow['app_type'];
            $android->app_version = $androidRow['app_version'];
            $android->status = $androidRow['status'];
            $android->created_at = $androidRow['created_at'];

            $result->android[] = $android;

        }    

        return $result;
      
    }

    //Get All Data
    public function getIos(): SoftwareIosData
    {
        $ios = $this->repository->getIos();

        $result = new SoftwareIosData();

        foreach ($ios as $iosRow) {
            $ios = new SoftwareIosDataRead();
            $ios->id  = $iosRow['id'];
            $ios->app_type = $iosRow['app_type'];
            $ios->app_version = $iosRow['app_version'];
            $ios->status = $iosRow['status'];
            $ios->created_at = $iosRow['created_at'];

            $result->ios[] = $ios;

        }    

        return $result;
      
    }

    // //APP Type Insert Validation
    // public function apptypeInsertValid(string $apptype): int
    // {
    //     $apptype_count = $this->repository->apptypeInsertValid($apptype);

    //     return $apptype_count;
    // }

    
    //Version Insert Validation
    public function versionInsertValid(string $version): int
    {
        $version_count = $this->repository->versionInsertValid($version);

        return $version_count;
    }

    
    //Insert Data
    public function insertSoftwareversion(array $data): int
    {
        
        // $this->createValidator->validateCreateData($data);

        $software = $this->repository->insertSoftwareversion($data);

        $this->logger->info(sprintf('Software created successfully: %s', $software));

        return $software;
    }
}
