<?php

namespace App\Domain\Rummy_Game\Gameroom_Free\Service;

//Data
use App\Domain\Rummy_Game\Gameroom_Free\Data\GameRoomFreeData;
use App\Domain\Rummy_Game\Gameroom_Free\Data\GameRoomFreeDataRead;

//Repository
use App\Domain\Rummy_Game\Gameroom_Free\Repository\GameRoomFreeRepository;

//Validator
use App\Domain\Rummy_Game\Gameroom_Free\Validator\GameRoomFreeCreateValidator;
use App\Domain\Rummy_Game\Gameroom_Free\Validator\GameRoomFreeUpdateValidator;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;

final class GameRoomFreeService
{
    private GameRoomFreeRepository $repository;
    private GameRoomFreeCreateValidator $createValidator;
    private GameRoomFreeUpdateValidator $updateValidator;

    

    private LoggerInterface $logger;

    public function __construct(GameRoomFreeRepository $repository, GameRoomFreeCreateValidator $createValidator,  GameRoomFreeUpdateValidator $updateValidator, LoggerFactory $loggerFactory) // 
    {
        $this->repository = $repository;
        $this->createValidator = $createValidator;
        $this->updateValidator = $updateValidator;

        $this->logger = $loggerFactory->addFileHandler('Rummy_Game/Gameroom_Free/GameRoomFree.log')->createLogger();
    }

    //Get All Data
    
    //Get All Data
    public function getGamefreeroom(array $data): array
    {
        $result = $this->repository->getGamefreeroom($data);

        return $result;
       
    }
  

 
    //Insert Data
    public function insertfreeGameroom(array $data): int
    {
        
        $this->createValidator->validateCreateData($data);

        $freegame = $this->repository->insertfreeGameroom($data);

        $this->logger->info(sprintf('FreeGameRoom created successfully: %s', $freegame));

        return $freegame;
    }


    //Update Data
    public function updatefreeGameroom(int $freegameid, array $data): int
    { 
        $this->updateValidator->validateUpdateData($data);

        $freegame = $this->repository->updatefreeGameroom($freegameid, $data);

        $this->logger->info(sprintf('FreeGameRoom updated successfully: %s', $freegameid));

        return $freegame;
    }

    // Status Change
    public function gamefreeroomStatus(int $freegameId, int $status): int
    {
        $freegame = $this->repository->gamefreeroomStatus($freegameId, $status);

        return  $freegame; 
    }


    //Delete Data
    public function deletefreeGameroom(int $freegameid): int
    {
        $freegame = $this->repository->deletefreeGameroom($freegameid);

        $this->logger->info(sprintf('FreeGameRoom delete successfully: %s', $freegameid));

        return $freegame;
    }

}
