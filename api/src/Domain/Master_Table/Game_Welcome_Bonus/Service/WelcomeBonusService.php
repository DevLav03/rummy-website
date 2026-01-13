<?php

namespace App\Domain\Master_Table\Game_Welcome_Bonus\Service;

//Data
use App\Domain\Master_Table\Game_Welcome_Bonus\Data\WelcomeBonusData;
use App\Domain\Master_Table\Game_Welcome_Bonus\Data\WelcomeBonusDataRead;

//Repository
use App\Domain\Master_Table\Game_Welcome_Bonus\Repository\WelcomeBonusRepository;

//Validator
use App\Domain\Master_Table\Game_Welcome_Bonus\Validator\WelcomeBonusCreateValidator;
use App\Domain\Master_Table\Game_Welcome_Bonus\Validator\WelcomeBonusUpdateValidator;
use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;

final class WelcomeBonusService
{
    private WelcomeBonusRepository $repository;
    private WelcomeBonusCreateValidator $createValidator;
    private WelcomeBonusUpdateValidator $updateValidator;

    

    private LoggerInterface $logger;

    public function __construct(WelcomeBonusRepository $repository, WelcomeBonusCreateValidator $createValidator, WelcomeBonusUpdateValidator $updateValidator,  LoggerFactory $loggerFactory) // 
    {
        $this->repository = $repository;
        $this->createValidator = $createValidator;
        $this->updateValidator = $updateValidator;

        $this->logger = $loggerFactory->addFileHandler('Master_Table/Game_Welcome_Bonus/GameBonus.log')->createLogger();
    }

    //Get All Data
    public function getWelcomeBonus(): WelcomeBonusData
    {
        $welcome = $this->repository->getWelcomeBonus();

        $result = new WelcomeBonusData();

        foreach ($welcome as $welcomeRow) {
            $welcome = new WelcomeBonusDataRead();
            $welcome->id = $welcomeRow['id'];
            $welcome->deposit_number = $welcomeRow['deposit_number'];
            $welcome->bonus_type = $welcomeRow['bonus_type'];
            $welcome->start_cash = $welcomeRow['start_cash'];
            $welcome->end_cash = $welcomeRow['end_cash'];
            $welcome->bonus_percentage = $welcomeRow['bonus_percentage'];
            $welcome->maximum_bonus = $welcomeRow['maximum_bonus'];
            $welcome->instant_cash_percentage = $welcomeRow['instant_cash_percentage'];
            $welcome->maximum_instant = $welcomeRow['maximum_instant'];
            $welcome->is_active = $welcomeRow['is_active'];
            $welcome->is_deleted = $welcomeRow['is_deleted'];
            $welcome->order_by = $welcomeRow['order_by'];
            $welcome->added_by = $welcomeRow['added_by'];
            $welcome->last_update = $welcomeRow['last_update'];
            $welcome->added_on = $welcomeRow['added_on'];
            $welcome->last_updated_on = $welcomeRow['last_updated_on'];
            

            $result->welcome[] = $welcome;
        }

        return $result;
       
    }

    //Insert Data
    public function insertWelcomeBonus(array $data): int
    {
        
        $this->createValidator->validateCreateData($data);

        $welcome = $this->repository->insertWelcomeBonus($data);

        $this->logger->info(sprintf('WelcomeBonus created successfully: %s', $welcome));

        return $welcome;
    }




    //Update Data
    public function updateWelcomeBonus(int $welcomebonusId, array $data): int
    { 
        $this->updateValidator->validateUpdateData($data);

        $welcome = $this->repository->updateWelcomeBonus($welcomebonusId, $data);

        $this->logger->info(sprintf('WelcomeBonus updated successfully: %s', $welcomebonusId));

        return $welcome;
    }


 

    //Delete Data
    public function deleteWelcomeBonus(int $welcomebonusId): int
    {
        $admin = $this->repository->deleteWelcomeBonus($welcomebonusId);

        $this->logger->info(sprintf('WelcomeBonus delete successfully: %s', $welcomebonusId));

        return $admin;
    }

 
}
