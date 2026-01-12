<?php

namespace App\Domain\Master_Table\Master_Game_State\Service;

//Data
use App\Domain\Master_Table\Master_Game_State\Data\StateData;
use App\Domain\Master_Table\Master_Game_State\Data\StateDataRead;

//Validator
use App\Domain\Master_Table\Master_Game_State\Validator\StateCreateValidator;
use App\Domain\Master_Table\Master_Game_State\Validator\StateUpdateValidator;

//Repository
use App\Domain\Master_Table\Master_Game_State\Repository\StateRepository;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;


final class StateService
{
    private StateRepository $repository;
    private StateCreateValidator $createValidator;
    private StateUpdateValidator $updateValidator;

    private LoggerInterface $logger;

    public function __construct(StateRepository $repository, LoggerFactory $loggerFactory, StateCreateValidator $createValidator, StateUpdateValidator $updateValidator)
    {
        $this->repository = $repository;
        $this->createValidator = $createValidator;
        $this->updateValidator = $updateValidator;

        $this->logger = $loggerFactory->addFileHandler('Master_Table/Master_Game_State/State.log')->createLogger();
    }

    //Get All Data
    public function getStates(): StateData
    {
        $state = $this->repository->getStates();

        $result = new StateData();

        foreach ($state as $stateRow) {
            $state = new StateDataRead();
            $state->id = $stateRow['id'];
            $state->state_code = $stateRow['state_code'];
            $state->state_name = $stateRow['state_name'];
            $state->user_alert_message = $stateRow['user_alert_message'];
            $state->status = $stateRow['status'];

           

            $result->state[] = $state;
        }

        return $result;
       
    }

    //Get One Data
    public function getOneState(int $stateId): StateData
    {
        $state = $this->repository->getOneState($stateId);

        $result = new StateData();

        foreach ($state as $stateRow) {
            $state = new StateDataRead();
            $state->id = $stateRow['id'];
            $state->state_code = $stateRow['state_code'];
            $state->state_name = $stateRow['state_name'];
            $state->user_alert_message = $stateRow['user_alert_message'];
            $state->status = $stateRow['status'];

           
            $result->state[] = $state;
        }

        return $result;
    }


    //Update Data
    public function updateState(int $stateid, array $data): int
    { 
        $this->updateValidator->validateUpdateData($data);

        $state = $this->repository->updateState($stateid, $data);

        $this->logger->info(sprintf('State updated successfully: %s', $stateid));

        return $state;
    }

  

    // Status Change
     public function ChangestateStatus(int $stateid, int $status): int
    {
        $bonustype = $this->repository->ChangestateStatus($stateid, $status);
 
        return  $bonustype; 
    }

   

}
