<?php

namespace App\Domain\Master_Table\Master_Game_Bonus\Service;

//Data
use App\Domain\Master_Table\Master_Game_Bonus\Data\BonusTypeData;
use App\Domain\Master_Table\Master_Game_Bonus\Data\BonusTypeDataRead;

//Repository
use App\Domain\Master_Table\Master_Game_Bonus\Repository\BonusTypeRepository;

//Validator
use App\Domain\Master_Table\Master_Game_Bonus\Validator\BonusTypeCreateValidator;
use App\Domain\Master_Table\Master_Game_Bonus\Validator\BonusTypeUpdateValidator;
use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;

final class BonusTypeService
{
    private BonusTypeRepository $repository;
    private BonusTypeCreateValidator $createValidator;
    private BonusTypeUpdateValidator $updateValidator;

    

    private LoggerInterface $logger;

    public function __construct(BonusTypeRepository $repository, BonusTypeCreateValidator $createValidator, BonusTypeUpdateValidator $updateValidator, LoggerFactory $loggerFactory) // 
    {
        $this->repository = $repository;
        $this->createValidator = $createValidator;
        $this->updateValidator = $updateValidator;

        $this->logger = $loggerFactory->addFileHandler('Master_Table/Master_Game_Bonus/GameBonus.log')->createLogger();
    }

    //Get All Data
    public function getBonusType(): BonusTypeData
    {
        $bonus = $this->repository->getBonusType();

        $result = new BonusTypeData();

        foreach ($bonus as $bonusRow) {
            $bonus = new BonusTypeDataRead();
            $bonus->id = $bonusRow['id'];
            $bonus->name = $bonusRow['name'];
        

            $result->bonus[] = $bonus;
        }

        return $result;
       
    }

    //Insert Data
    public function insertBonusType(array $data): int
    {
        
        $this->createValidator->validateCreateData($data);

        $bonustype = $this->repository->insertBonusType($data);

        $this->logger->info(sprintf('BonusType created successfully: %s', $bonustype));

        return $bonustype;
    }




    //Update Data
    public function updateBonusType(int $bonustypeId, array $data): int
    { 
        $this->updateValidator->validateUpdateData($data);

        $bonustype = $this->repository->updateBonusType($bonustypeId, $data);

        $this->logger->info(sprintf('BonusType updated successfully: %s', $bonustypeId));

        return $bonustype;
    }


 

    // Status Change
    public function bonustypeStatus(int $bonustypeId, int $status): int
    {
        $bonustype = $this->repository->bonustypeStatus($bonustypeId, $status);

        return  $bonustype; 
    }

 
}
