<?php

namespace App\Domain\Admin_Panel\Tournament\Service;

//Data
use App\Domain\Admin_Panel\Tournament\Data\TournamentData;
use App\Domain\Admin_Panel\Tournament\Data\TournamentDataRead;

//Validator
use App\Domain\Admin_Panel\Tournament\Validator\CreateValidator;
use App\Domain\Admin_Panel\Tournament\Validator\TournamentValidator;

//Repository
use App\Domain\Admin_Panel\Tournament\Repository\TournamentRepository;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;

final class TournamentService
{
    private TournamentRepository $repository;
    private CreateValidator $createValidator;
    private TournamentValidator $tournamentValidator;

    private LoggerInterface $logger;

    public function __construct(TournamentRepository $repository, CreateValidator $createValidator, TournamentValidator $tournamentValidator,  LoggerFactory $loggerFactory)
    {
        $this->repository = $repository;
        $this->createValidator = $createValidator;
        $this->tournamentValidator = $tournamentValidator;

        $this->logger = $loggerFactory->addFileHandler('Admin_Panel/Tournament/Tournament.log')->createLogger();
    }

    //Get All Data
    public function getTournaments(array $data): TournamentData
    {
        $this->tournamentValidator->validateData($data);

        $tournaments = $this->repository->getTournaments($data);

        $result = new TournamentData();
    
        foreach($tournaments as $tourneyRow){

            $tourney = new TournamentDataRead();

            $tourney->id = $tourneyRow['id'];
            $tourney->title = $tourneyRow['title'];
            $tourney->start_date = $tourneyRow['start_date'];
            $tourney->start_time = $tourneyRow['start_time'];
            $tourney->registration_start_date = $tourneyRow['registration_start_date'];
            $tourney->registration_start_time = $tourneyRow['registration_start_time'];
            $tourney->registration_end_date = $tourneyRow['registration_end_date'];
            $tourney->registration_end_time = $tourneyRow['registration_end_time'];
            $tourney->entry_fees = $tourneyRow['entry_fees'];
            $tourney->paid_amount = $tourneyRow['paid_amount'];
            $tourney->no_of_players = $tourneyRow['no_of_players'];
            $tourney->description = $tourneyRow['description'];
            $tourney->position = $tourneyRow['position'];
            $tourney->price = $tourneyRow['price'];
            $tourney->status = $tourneyRow['status'];

            $result->tournaments[] = $tourney;
        } 

        return $result;
    
    }

    public function getTournamentsCount(array $data): int
    {
        $total = $this->repository->getTournamentsCount($data);
        return  $total ; 
    }


    //Get One Data
    public function getOneTournament(int $tournamentId): TournamentData
    {
        $tournaments = $this->repository->getOneTournament($tournamentId);
        
        $result = new TournamentData();
    
        foreach($tournaments as $tourneyRow){

            $tourney = new TournamentDataRead();

            $tourney->id = $tourneyRow['id'];
            $tourney->title = $tourneyRow['title'];
            $tourney->start_date = $tourneyRow['start_date'];
            $tourney->start_time = $tourneyRow['start_time'];
            $tourney->registration_start_date = $tourneyRow['registration_start_date'];
            $tourney->registration_start_time = $tourneyRow['registration_start_time'];
            $tourney->registration_end_date = $tourneyRow['registration_end_date'];
            $tourney->registration_end_time = $tourneyRow['registration_end_time'];
            $tourney->entry_fees = $tourneyRow['entry_fees'];
            $tourney->paid_amount = $tourneyRow['paid_amount'];
            $tourney->no_of_players = $tourneyRow['no_of_players'];
            $tourney->description = $tourneyRow['description'];
            $tourney->position = $tourneyRow['position'];
            $tourney->price = $tourneyRow['price'];
            $tourney->status = $tourneyRow['status'];

            $result->tournaments[] = $tourney;
        } 

        return $result;
        
    }

    //Insert Data
    public function insertTournament(array $data): int
    {
        $this->createValidator->validateAddTournament($data);

        $tournamentId = $this->repository->insertTournament($data);

        $this->logger->info(sprintf('Tournament created successfully: %s', $tournamentId));

        return $tournamentId;
    }

    //Update Data
    public function updateTournament(int $tournamentId, array $data): int
    { 

        $tournament = $this->repository->updateTournament($tournamentId, $data);

        $this->logger->info(sprintf('Tournament updated successfully: %s', $tournamentId));

        return $tournament;
    }
   
    //block and unblock
    public function blockTournament(int $tournament_id, int $tourney_status): int
    {
        $tournament = $this->repository->blockTournamentById($tournament_id, $tourney_status);

        $this->logger->info(sprintf('Blocked or Unblocked Successfully: %s', $tournament_id));

        return $tournament;
    }
}
