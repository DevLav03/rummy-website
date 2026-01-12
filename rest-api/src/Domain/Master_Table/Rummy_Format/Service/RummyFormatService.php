<?php

namespace App\Domain\Master_Table\Rummy_Format\Service;

//Data
use App\Domain\Master_Table\Rummy_Format\Data\RummyFormatData;
use App\Domain\Master_Table\Rummy_Format\Data\RummyFormatDataRead;

//Validator
use App\Domain\Master_Table\Rummy_Format\Validator\RummyFormatCreateValidator;
use App\Domain\Master_Table\Rummy_Format\Validator\RummyFormatUpdateValidator;

//Repository
use App\Domain\Master_Table\Rummy_Format\Repository\RummyFormatRepository;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;

final class RummyFormatService
{
    private RummyFormatRepository $repository;
    private RummyFormatCreateValidator $createValidator;
    private RummyFormatUpdateValidator $updateValidator;


    private LoggerInterface $logger;

    public function __construct(RummyFormatRepository $repository, RummyFormatCreateValidator $createValidator, RummyFormatUpdateValidator $updateValidator, LoggerFactory $loggerFactory)
    {
        $this->repository = $repository;
        $this->createValidator = $createValidator;
        $this->updateValidator = $updateValidator;
        
        $this->logger = $loggerFactory->addFileHandler('Master_Table/Rummy_Format/Rummy_Format.log')->createLogger();
    }

    //Get All Data
    public function getRummyFormat(): RummyFormatData
    {
        $formats = $this->repository->getRummyFormat();

        $result = new RummyFormatData();

        foreach ($formats as $format) {

            $rummy_format = new RummyFormatDataRead();

            $rummy_format->id = $format['id'];
            $rummy_format->name = $format['name'];
            $rummy_format->description = $format['description'];
            
            $result->format[] = $format;
        }

        return $result;
    } 

    public function getActiveRummyFormat(): RummyFormatData
    {
        $formats = $this->repository->getActiveRummyFormat();

        $result = new RummyFormatData();

        foreach ($formats as $format) {

            $rummy_format = new RummyFormatDataRead();

            $rummy_format->id = $format['id'];
            $rummy_format->name = $format['name'];
            $rummy_format->description = $format['description'];
            
            $result->format[] = $format;
        }

        return $result;
    } 


    //Insert Data
    public function insertRummyFormat(array $data): int
    {
        
        $this->createValidator->validateCreateData($data);

        $formatId = $this->repository->insertRummyFormat($data);

        $this->logger->info(sprintf('Rummy Format created successfully: %s', $formatId));

        return $formatId;
    }


    //Update Data
    public function updateRummyFormat(int $formatId, array $data): int
    { 
        $this->updateValidator->validateUpdateData($data);

        $formats = $this->repository->updateRummyFormat($formatId, $data);

        $this->logger->info(sprintf('Rummy Format updated successfully: %s', $formatId));

        return $formats;
    }


    //Status Change
    public function rummyFormatStatus(int $formatId, int $status): int
    {
        $formats = $this->repository->rummyFormatStatus($formatId, $status);

        $this->logger->info(sprintf('Rummy Format change status successfully: %s', $formatId, $status));

        return  $formats; 
    }



    //Delete Data
    public function deleteRummyFormat(int $formatId): int
    {
        $formats = $this->repository->deleteRummyFormat($formatId);

        $this->logger->info(sprintf('Rummy Format delete successfully: %s', $formatId));

        return $formats;
    }
  
}
