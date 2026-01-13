<?php

namespace App\Domain\Master_Table\Master_Game_Types\Service;

//Data
use App\Domain\Master_Table\Master_Game_Types\Data\GameTypesData;
use App\Domain\Master_Table\Master_Game_Types\Data\GameTypesDataRead;

//Validator
use App\Domain\Master_Table\Master_Game_Types\Validator\GameTypeCreateValidator;
use App\Domain\Master_Table\Master_Game_Types\Validator\GameTypeUpdateValidator;
use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;

//Repository
use App\Domain\Master_Table\Master_Game_Types\Repository\GameTypesRepository;

final class GameTypesService
{
    private GameTypesRepository $repository;
    private GameTypeCreateValidator $createValidator;
    private GameTypeUpdateValidator $updateValidator;


    private LoggerInterface $logger;

    public function __construct(GameTypesRepository $repository, GameTypeCreateValidator $createValidator, GameTypeUpdateValidator $updateValidator, LoggerFactory $loggerFactory)
    {
        $this->repository = $repository;
        $this->createValidator = $createValidator;
        $this->updateValidator = $updateValidator;
        
        $this->logger = $loggerFactory->addFileHandler('Master_Table/Master_Game_Types/MatchType.log')->createLogger();
    }

    //Get All Data
    public function getGameTypes(): GameTypesData
    {
        $gameTypes = $this->repository->getGameTypes();

        $result = new GameTypesData();

        foreach ($gameTypes as $gameType) {

            $game = new GameTypesDataRead();

            $game->id = $gameType['id'];
            $game->name = $gameType['name'];
            $game->description = $gameType['description'];
            
            $result->gameTypes[] = $gameType;
        }

        return $result;
    } 


    //Insert Data
    public function insertGameType(array $data): int
    {
        
        $this->createValidator->validateCreateData($data);

        $gameId = $this->repository->insertGameType($data);

        $this->logger->info(sprintf('GameType created successfully: %s', $gameId));

        return $gameId;
    }


    //Update Data
    public function updateGameType(int $gameid, array $data): int
    { 
        $this->updateValidator->validateUpdateData($data);

        $gametype = $this->repository->updateGameType($gameid, $data);

        $this->logger->info(sprintf('GameType updated successfully: %s', $gameid));

        return $gametype;
    }


    //Status Change
    public function gameStatus(int $gameId, int $status): int
    {
        $gametype = $this->repository->gameStatus($gameId, $status);

        return  $gametype; 
    }



    //Delete Data
    public function deleteGameType(int $gameId): int
    {
        $gametype = $this->repository->deleteGameType($gameId);

        $this->logger->info(sprintf('GameType delete successfully: %s', $gameId));

        return $gametype;
    }
  
}
