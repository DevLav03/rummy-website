<?php

namespace App\Domain\Master_Table\Rummy_Max_Seats\Service;

//Data
use App\Domain\Master_Table\Rummy_Max_Seats\Data\RummyMaxSeatsData;
use App\Domain\Master_Table\Rummy_Max_Seats\Data\RummyMaxSeatsDataRead;

//Repository
use App\Domain\Master_Table\Rummy_Max_Seats\Repository\RummyMaxSeatsRepository;

//Validator
use App\Domain\Master_Table\Rummy_Max_Seats\Validator\RummyMaxSeatsCreateValidator;
use App\Domain\Master_Table\Rummy_Max_Seats\Validator\RummyMaxSeatsUpdateValidator;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;

final class RummyMaxSeatsService
{
    private RummyMaxSeatsRepository $repository;
    private RummyMaxSeatsCreateValidator $createValidator;
    private RummyMaxSeatsUpdateValidator $updateValidator;

    private LoggerInterface $logger;

    //

    public function __construct(RummyMaxSeatsRepository $repository, LoggerFactory $loggerFactory, RummyMaxSeatsCreateValidator $createValidator, RummyMaxSeatsUpdateValidator $updateValidator) 
    {
        $this->repository = $repository;
        $this->createValidator = $createValidator;
        $this->updateValidator = $updateValidator;

        $this->logger = $loggerFactory->addFileHandler('Master_Table/Rummy_Max_Seats/Rummy_Max_Seats.log')->createLogger();
    }

    //Get All Data
    public function getMaxSeats(): RummyMaxSeatsData
    {
        $max_seats = $this->repository->getMaxSeats();

        $result = new RummyMaxSeatsData();

        foreach ($max_seats as $max_seat) {

            $maxSeats = new RummyMaxSeatsDataRead();

            $maxSeats->id = $max_seat['id'];
            $maxSeats->seats = $max_seat['seats'];
            $maxSeats->active = $max_seat['active'];
            $maxSeats->created_at = $max_seat['create_at'];

            $result->max_seats[] = $max_seat;
        }

        return $result;
       
    }

    //Get Active Data
    public function getActiveMaxSeats(): RummyMaxSeatsData
    {
        $max_seats = $this->repository->getActiveMaxSeats();

        $result = new RummyMaxSeatsData();

        foreach ($max_seats as $max_seat) {

            $maxSeats = new RummyMaxSeatsDataRead();

            $maxSeats->id = $max_seat['id'];
            $maxSeats->seats = $max_seat['seats'];
            $maxSeats->active = $max_seat['active'];
            $maxSeats->created_at = $max_seat['create_at'];

            $result->max_seats[] = $max_seat;
        }

        return $result;
       
    }


    //Insert Data
    public function insertMaxSeats(array $data): int
    {
        
        $this->createValidator->validateCreateData($data);

        $seatsId = $this->repository->insertMaxSeats($data);

        $this->logger->info(sprintf('Rummy Max Seats created successfully: %s', $seatsId));

        return $seatsId;
    }

    //Update Data
    public function updateMaxSeats(int $seatsId, array $data): int
    { 
        $this->updateValidator->validateUpdateData($data);

        $max_seats = $this->repository->updateMaxSeats($seatsId, $data);

        $this->logger->info(sprintf('Rummy Max Seats updated successfully: %s', $seatsId));

        return $max_seats;
    }

    //Remove Admin IP Restrict Status
    public function maxSeatsStatus(int $seatsId, int $status): int
    {
        $max_seats = $this->repository->maxSeatsStatus($seatsId, $status);

        return  $max_seats; 
    }

    //Delete Data
    public function deleteMaxSeats(int $seatsId): int
    {
        $max_seats = $this->repository->deleteMaxSeats($seatsId);

        $this->logger->info(sprintf('Rummy Max Seats delete successfully: %s', $seatsId));

        return $max_seats;
    }
}
