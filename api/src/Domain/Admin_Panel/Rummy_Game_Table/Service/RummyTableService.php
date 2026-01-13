<?php

namespace App\Domain\Admin_Panel\Rummy_Game_Table\Service;

//Validator
use App\Domain\Admin_Panel\Rummy_Game_Table\Validator\RummyTableValidator;

//Repository
use App\Domain\Admin_Panel\Rummy_Game_Table\Repository\RummytableRepository;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;

final class RummyTableService
{
    private RummytableRepository $repository;
    private RummyTableValidator $rummyTableValidator;

    private LoggerInterface $logger;

    public function __construct(RummytableRepository $repository, RummyTableValidator $rummyTableValidator,  LoggerFactory $loggerFactory)
    {
        $this->repository = $repository;
        $this->rummyTableValidator = $rummyTableValidator;

        $this->logger = $loggerFactory->addFileHandler('Admin_Panel/Rummy_Game_Table/RummyTable.log')->createLogger();
    }

    //Insert Data
    public function insertRummytable(array $data): int
    {      
        $this->rummyTableValidator->validateData($data);

        $rummytableId = $this->repository->insertRummytable($data);

        $this->logger->info(sprintf('Rummy Table created successfully: %s', $rummytableId));

        return $rummytableId;
    }

    //Change status
    public function changeStatus(int $id, int $status): int
    {
        $table = $this->repository->changeStatus($id, $status);

        $this->logger->info(sprintf('Change Status Successfully: %s', $id));

        return $table;
    }

    //Delete Data
    public function deleteTable(int $id): int
    {
        $deleted = $this->repository->deleteTable($id);
        
        $this->logger->info(sprintf('Rummy Table delete successfully: %s', $id));

        return $deleted;
    }
   
}
