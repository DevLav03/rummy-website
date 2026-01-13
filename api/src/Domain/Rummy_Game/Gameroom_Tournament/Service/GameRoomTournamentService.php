<?php

namespace App\Domain\Rummy_Game\Gameroom_Tournament\Service;

//Data
use App\Domain\Rummy_Game\Gameroom_Tournament\Data\GameRoomTournamentData;
use App\Domain\Rummy_Game\Gameroom_Tournament\Data\GameRoomTournamentDataRead;

//Repository
use App\Domain\Rummy_Game\Gameroom_Tournament\Repository\GameRoomTournamentRepository;

//Validator
use App\Domain\Rummy_Game\Gameroom_Tournament\Validator\GameRoomTournamentCreateValidator;


use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;

final class GameRoomTournamentService
{
    private GameRoomTournamentRepository $repository;
    private GameRoomTournamentCreateValidator $createValidator;

    private LoggerInterface $logger;

    public function __construct(GameRoomTournamentRepository $repository, GameRoomTournamentCreateValidator $createValidator,  LoggerFactory $loggerFactory) 
    {
        $this->repository = $repository;
        $this->createValidator = $createValidator;
       
        $this->logger = $loggerFactory->addFileHandler('Rummy_Game/Gameroom_Tournament/GameRoomTournament.log')->createLogger();
    }

    public function getTourneyGameType(): array{

        $result = $this->repository->getTourneyGameType();
        return $result;
    }

    public function getTourneyMaxPlayer(int $game_id): array{
        
        $result = $this->repository->getTourneyMaxPlayer($game_id);
        return $result;
    }

    public function getTourneyEntryFees(int $game_id, int $max_player): array{
        
        $result = $this->repository->getTourneyEntryFees($game_id, $max_player);
        return $result;
    }

    //Get All Data
    public function getTourneyroom(array $data): array
    {
        $result = $this->repository->getTourneyroom($data);

        return $result;
       
    }
  

 
    //Insert Data
    public function insertTourneyroom(array $data): int
    {
        
        $this->createValidator->validateCreateData($data);

        $tourneygame = $this->repository->insertTourneyroom($data);

        $this->logger->info(sprintf('TourneyGameRoom created successfully: %s', $tourneygame));

        return $tourneygame;
    }


    

    // Status Change
    public function TourneyroomActive(int $tourneygameId, int $active): int
    {
        $tourneygame = $this->repository->TourneyroomActive($tourneygameId, $active);

        return  $tourneygame; 
    }


    //Delete Data
    public function deleteTourneyroom(int $tourneygameid): int
    {
        $tourneygame = $this->repository->deleteTourneyroom($tourneygameid);

        $this->logger->info(sprintf('TourneyGameRoom delete successfully: %s', $tourneygameid));

        return $tourneygame;
    }

}
