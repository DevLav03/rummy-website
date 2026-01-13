<?php

namespace App\Domain\Master_Table\Master_Game_Match_Types\Service;

//Data
use App\Domain\Master_Table\Master_Game_Match_Types\Data\MatchTypeData;
use App\Domain\Master_Table\Master_Game_Match_Types\Data\MatchTypeDataRead;

use App\Domain\Master_Table\Master_Game_Match_Types\Repository\MatchTypesRepository;

//Validator
use App\Domain\Master_Table\Master_Game_Match_Types\Validator\MatchTypeCreateValidator;
use App\Domain\Master_Table\Master_Game_Match_Types\Validator\MatchTypeUpdateValidator;
use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;

final class MatchTypesService
{
    private MatchTypesRepository $repository;
    private MatchTypeCreateValidator $createValidator;
    private MatchTypeUpdateValidator $updateValidator;

    

    private LoggerInterface $logger;

    public function __construct(MatchTypesRepository $repository, MatchTypeCreateValidator $createValidator, MatchTypeUpdateValidator $updateValidator, LoggerFactory $loggerFactory) //, 
    {
        $this->repository = $repository;
        $this->createValidator = $createValidator;
        $this->updateValidator = $updateValidator;

        $this->logger = $loggerFactory->addFileHandler('Master_Table/Master_Game_Match_Types/MatchType.log')->createLogger();
    }

    //Get All Data
    public function getMatchType(): MatchTypeData
    {
        $matchtype = $this->repository->getMatchType();

        $result = new MatchTypeData();

        foreach ($matchtype as $matchtypeRow) {
            $matchtype = new MatchTypeDataRead();
            $matchtype->id = $matchtypeRow['type_id'];
            $matchtype->name = $matchtypeRow['name'];
            $matchtype->description = $matchtypeRow['description'];
            $matchtype->active = $matchtypeRow['active'];
            $matchtype->created_at = $matchtypeRow['created_at'];

            $result->matchtype[] = $matchtype;
        }

        return $result;
       
    }


    //Insert Data
    public function insertMatchType(array $data): int
    {
        
        $this->createValidator->validateCreateData($data);

        $matchtypeId = $this->repository->insertMatchType($data);

        $this->logger->info(sprintf('MatchType created successfully: %s', $matchtypeId));

        return $matchtypeId;
    }

    //Update Data
    public function updateMatchType(int $matchtypeId, array $data): int
    { 
        $this->updateValidator->validateUpdateData($data);

        $matchtype = $this->repository->updateMatchType($matchtypeId, $data);

        $this->logger->info(sprintf('MatchType updated successfully: %s', $matchtypeId));

        return $matchtype;
    }

    //Remove Admin IP Restrict Status
    public function matchStatus(int $matchId, int $status): int
    {
        $matchtype = $this->repository->matchStatus($matchId, $status);

        return  $matchtype; 
    }

    //Delete Data
    public function deleteMatchType(int $matchtypeId): int
    {
        $matchtype = $this->repository->deleteMatchType($matchtypeId);

        $this->logger->info(sprintf('MatchType delete successfully: %s', $matchtypeId));

        return $matchtype;
    }
}
