<?php

namespace App\Domain\Rummy_Game\Gameroom_Cash\Service;

//Data
use App\Domain\Rummy_Game\Gameroom_Cash\Data\GameRoomData;
use App\Domain\Rummy_Game\Gameroom_Cash\Data\GameRoomDataRead;

//Repository
use App\Domain\Rummy_Game\Gameroom_Cash\Repository\GameRoomRepository;

//Validator
use App\Domain\Rummy_Game\Gameroom_Cash\Validator\GameRoomCreateValidator;
use App\Domain\Rummy_Game\Gameroom_Cash\Validator\GameRoomUpdateValidator;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;

final class GameRoomService
{
    private GameRoomRepository $repository;
    private GameRoomCreateValidator $createValidator;
    private GameRoomUpdateValidator $updateValidator;

    

    private LoggerInterface $logger;

    public function __construct(GameRoomRepository $repository, GameRoomCreateValidator $createValidator, GameRoomUpdateValidator $updateValidator, LoggerFactory $loggerFactory) // 
    {
        $this->repository = $repository;
        $this->createValidator = $createValidator;
        $this->updateValidator = $updateValidator;

        $this->logger = $loggerFactory->addFileHandler('Rummy_Game/Gameroom_Cash/GameRoom.log')->createLogger();
    }

    //Get Game Type
    public function getCashGameType(): array{

        $result = $this->repository->getCashGameType();
        return $result;
    }

    //Get Max Player
    public function getCashMaxPlayer(int $game_id): array{
        
        $result = $this->repository->getCashMaxPlayer($game_id);
        return $result;
    }

    //Get Entry Fees
    public function getCashEntryFees(int $game_id, int $max_player): array{
        
        $result = $this->repository->getCashEntryFees($game_id, $max_player);
        return $result;
    }
    
    //Get All Data
    public function getGameroom(array $data): array
    {
        $result = $this->repository->getGameroom($data);

        return $result;
       
    }
  

 
    //Insert Data
    public function insertGameroom(array $data): int
    {
        
        $this->createValidator->validateCreateData($data);

        $gameroom = $this->repository->insertGameroom($data);

        $this->logger->info(sprintf('GameRoom created successfully: %s', $gameroom));

        return $gameroom;
    }

    public function getTableCount(array $data): int
    {
        $gameroom = $this->repository->getTableCount($data);
        return $gameroom;
    }


    //Update Data
    public function updateGameroom(int $gameroomid, array $data): int
    { 
        $this->updateValidator->validateUpdateData($data);

        $gameroom = $this->repository->updateGameroom($gameroomid, $data);

        $this->logger->info(sprintf('GameRoom updated successfully: %s', $gameroomid));

        return $gameroom;
    }

    // Status Change
    public function gameroomStatus(int $gameroomId, int $status): int
    {
        $gameroom = $this->repository->gameroomStatus($gameroomId, $status);

        return  $gameroom; 
    }


    //Delete Data
    public function deleteGameroom(int $gameroomId): int
    {
        $gameroom = $this->repository->deleteGameroom($gameroomId);

        $this->logger->info(sprintf('GameRoom delete successfully: %s', $gameroomId));

        return $gameroom;
    }

}
