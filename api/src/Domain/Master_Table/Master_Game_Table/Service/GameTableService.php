<?php

namespace App\Domain\Master_Table\Master_Game_Table\Service;

//Data
use App\Domain\Master_Table\Master_Game_Table\Data\GameTableData;
use App\Domain\Master_Table\Master_Game_Table\Data\GameTableDataRead;

//Repository
use App\Domain\Master_Table\Master_Game_Table\Repository\GameTableRepository;

//Validator
use App\Domain\Master_Table\Master_Game_Table\Validator\GameTableCreateValidator;
use App\Domain\Master_Table\Master_Game_Table\Validator\GameTableUpdateValidator;
use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;

final class GameTableService
{
    private GameTableRepository $repository;
    private GameTableCreateValidator $createValidator;
    private GameTableUpdateValidator $updateValidator;

    

    private LoggerInterface $logger;

    public function __construct(GameTableRepository $repository, GameTableCreateValidator $createValidator, GameTableUpdateValidator $updateValidator, LoggerFactory $loggerFactory) //
    {
        $this->repository = $repository;
        $this->createValidator = $createValidator;
        $this->updateValidator = $updateValidator;

        $this->logger = $loggerFactory->addFileHandler('Master_Table/Master_Game_Table/GameTable.log')->createLogger();
    }

    //Get All Data
    public function getGameTable(array $data): array
    {
        $gametable = $this->repository->getGameTable($data);

        // $result = new GameTableData();

        // foreach ($gametable as $gametableRow) {
        //     $gametable = new GameTableDataRead();
        //     $gametable->id = $gametableRow['id'];
        //     $gametable->match_id = $gametableRow['match_id'];
        //     $gametable->game_type_name = $gametableRow['game_type_name'];
        //     $gametable->game_id = $gametableRow['game_id'];
        //     $gametable->game_match_name = $gametableRow['game_match_name'];
        //     $gametable->max_player = $gametableRow['max_player'];
        //     $gametable->entry_fees = $gametableRow['entry_fees'];
        //     $gametable->active = $gametableRow['active'];
        //     $gametable->created_at = $gametableRow['created_at'];

        //     $result->gametable[] = $gametable;
        // }

        return $gametable;
       
    }

    //Insert Data
    public function insertGameTable(array $data): int
    {
        
        $this->createValidator->validateCreateData($data);

        $gametable = $this->repository->insertGameTable($data);

        $this->logger->info(sprintf('GameTable created successfully: %s', $gametable));

        return $gametable;
    }

    //Match Insert Validation
    public function matchInsertValid(array $data): int
    {
        $match_count = $this->repository->matchInsertValid($data);

        return $match_count;
    }


    //Update Data
    public function updateGameTable(int $gametableId, array $data): int
    { 
        $this->updateValidator->validateUpdateData($data);

        $gametable = $this->repository->updateGameTable($gametableId, $data);

        $this->logger->info(sprintf('GameTable updated successfully: %s', $gametableId));

        return $gametable;
    }


    //Match Update Validation
    public function matchUpdateValid(array $data, int $gametable_id): int
    {
        
        $match_count = $this->repository->matchUpdateValid($data, $gametable_id);
        
        return $match_count;
    }


    // Status Change
    public function gametableStatus(int $gametableId, int $status): int
    {
        $matchtype = $this->repository->gametableStatus($gametableId, $status);

        return  $matchtype; 
    }

    //Delete Data
    public function deleteGameTable(int $gametableId): int
    {
        $gametable = $this->repository->deleteGameTable($gametableId);

        $this->logger->info(sprintf('MatchType delete successfully: %s', $gametableId));

        return $gametable;
    }
}
