<?php

namespace App\Domain\Rummy_Game\Deposit\Service;

//Data
use App\Domain\Rummy_Game\Deposit\Data\DepositData;
use App\Domain\Rummy_Game\Deposit\Data\DepositDataRead;

//Repository
use App\Domain\Rummy_Game\Deposit\Repository\DepositRepository;

//Validator
// use App\Domain\Rummy_Game\Deposit\Validator\DepositCreateValidator;
// use App\Domain\Rummy_Game\Deposit\Validator\DepositUpdateValidator;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;

final class DepositService
{
    private DepositRepository $repository;
    // private DepositCreateValidator $createValidator;
    // private DepositUpdateValidator $updateValidator;

    
             
    private LoggerInterface $logger;

    public function __construct(DepositRepository $repository,  LoggerFactory $loggerFactory) // DepositCreateValidator $createValidator, DepositUpdateValidator $updateValidator,
    {
        $this->repository = $repository;
        // $this->createValidator = $createValidator;
        // $this->updateValidator = $updateValidator;

        $this->logger = $loggerFactory->addFileHandler('Rummy_Game/Deposit/Deposit.log')->createLogger();
    }
    
    //Get All Data
    public function createOrder(array $data, int $user_id): int
    {

        $users = $this->repository->getUserDetail($user_id);

        $json_users = json_encode($users);
        //$str_users = json_decode($json_users);
        $data['customer_details']=$json_users;

        //print_r($data); exit;

        // $obj_users = $this->arrayToObject($users);

        $result = $this->repository->createOrder($data, $obj_users);


        return $result;
       
    }
  
    function arrayToObject($users)
    {
        $object= new stdClass();
        return array_to_obj($users,$object);
    }

 


}
?>