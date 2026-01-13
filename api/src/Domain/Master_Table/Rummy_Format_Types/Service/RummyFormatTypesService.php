<?php

namespace App\Domain\Master_Table\Rummy_Format_Types\Service;

//Data
use App\Domain\Master_Table\Rummy_Format_Types\Data\RummyFormatTypesData;
use App\Domain\Master_Table\Rummy_Format_Types\Data\RummyFormatTypesDataRead;

//Validator
use App\Domain\Master_Table\Rummy_Format_Types\Validator\RummyFormatTypesCreateValidator;
use App\Domain\Master_Table\Rummy_Format_Types\Validator\RummyFormatTypesUpdateValidator;

//Repository
use App\Domain\Master_Table\Rummy_Format_Types\Repository\RummyFormatTypesRepository;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;

final class RummyFormatTypesService
{
    private RummyFormatTypesRepository $repository;
    private RummyFormatTypesCreateValidator $createValidator;
    private RummyFormatTypesUpdateValidator $updateValidator;


    private LoggerInterface $logger;

    public function __construct(RummyFormatTypesRepository $repository, RummyFormatTypesCreateValidator $createValidator, RummyFormatTypesUpdateValidator $updateValidator, LoggerFactory $loggerFactory)
    {
        $this->repository = $repository;
        $this->createValidator = $createValidator;
        $this->updateValidator = $updateValidator;
        
        $this->logger = $loggerFactory->addFileHandler('Master_Table/Rummy_Format_Types/Rummy_Format_Types.log')->createLogger();
    }

    //Get All Data
    public function getRummyFormatTypes(int $format_id): RummyFormatTypesData
    {
        $format_types = $this->repository->getRummyFormatTypes($format_id);

        $result = new RummyFormatTypesData();

        foreach ($format_types as $format_type) {

            $formats = new RummyFormatTypesDataRead();

            $formats->id = $format_type['id'];
            $formats->name = $format_type['name'];
            $formats->description = $format_type['description'];
            $formats->format_id = $format_type['format_id'];
            $formats->format_name = $format_type['format_name'];
            $formats->created_at = $format_type['create_at'];
            
            $result->format_types[] = $format_type;
        }

        return $result;
    } 

    //Get All Data
    public function getActiveRummyFormatTypes(): RummyFormatTypesData
    {
        $format_types = $this->repository->getActiveRummyFormatTypes();

        $result = new RummyFormatTypesData();

        foreach ($format_types as $format_type) {

            $formats = new RummyFormatTypesDataRead();

            $formats->id = $format_type['id'];
            $formats->name = $format_type['name'];
            $formats->description = $format_type['description'];
            $formats->format_id = $format_type['format_id'];
            $formats->format_name = $format_type['format_name'];
            $formats->created_at = $format_type['create_at'];
            
            $result->format_types[] = $format_type;
        }

        return $result;
    } 

    //Insert Data
    public function insertRummyFormatTypes(array $data): int
    {
        
        $this->createValidator->validateCreateData($data);

        $formatTypeId = $this->repository->insertRummyFormatTypes($data);

        $this->logger->info(sprintf('Rummy Format Types created successfully: %s', $formatTypeId));

        return $formatTypeId;
    }


    //Update Data
    public function updateRummyFormatTypes(int $formatTypeId, array $data): int
    { 
        $this->updateValidator->validateUpdateData($data);

        $format_types = $this->repository->updateRummyFormatTypes($formatTypeId, $data);

        $this->logger->info(sprintf('Rummy Format Type updated successfully: %s', $formatTypeId));

        return $format_types;
    }


    //Status Change
    public function rummyFormatTypeStatus(int $format_type_id, int $status): int
    {
        $format_types = $this->repository->rummyFormatTypeStatus($format_type_id, $status);

        $this->logger->info(sprintf('Rummy Format Types change status successfully: %s', $format_type_id, $status));

        return  $format_types; 
    }



    //Delete Data
    public function deleteRummyFormatTypes(int $format_type_id): int
    {
        $format_types = $this->repository->deleteRummyFormatTypes($format_type_id);

        $this->logger->info(sprintf('Rummy Format Types delete successfully: %s', $format_type_id));

        return $format_types;
    }
  
}
