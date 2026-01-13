<?php

namespace App\Domain\Rummy_Game\Gameroom_PrivateTable\Service;

//Data
use App\Domain\Rummy_Game\Gameroom_PrivateTable\Data\GameRoomPrivateTableData;
use App\Domain\Rummy_Game\Gameroom_PrivateTable\Data\GameRoomPrivateTableDataRead;

//Repository
use App\Domain\Rummy_Game\Gameroom_PrivateTable\Repository\GameRoomPrivateTableRepository;

//Validator
use App\Domain\Rummy_Game\Gameroom_PrivateTable\Validator\GameRoomPrivateTableCreateValidator;


use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;

final class GameRoomPrivateTableService
{
    private GameRoomPrivateTableRepository $repository;
    private GameRoomPrivateTableCreateValidator $createValidator;
    
    

    private LoggerInterface $logger;

    public function __construct(GameRoomPrivateTableRepository $repository, GameRoomPrivateTableCreateValidator $createValidator, LoggerFactory $loggerFactory) // 
    {
        $this->repository = $repository;
        $this->createValidator = $createValidator;
       

        $this->logger = $loggerFactory->addFileHandler('Rummy_Game/Gameroom_PrivateTable/GameRoomPrivateTable.log')->createLogger();
    }

    
    //Insert Data
    public function insertPrivateTable(array $data, int $user_id): string
    {
        
        $this->createValidator->validateCreateData($data);

        $privatetable = $this->repository->insertPrivateTable($data, $user_id);

        $this->logger->info(sprintf('PrivateTableRoom created successfully: %s', $privatetable));

        return $privatetable;
    }

    //Enter Private Table
    
    public function enterPrivateTable(array $data): string
    {
        
        $privatetable = $this->repository->enterPrivateTable($data);

        return $privatetable;
    }
    

}
