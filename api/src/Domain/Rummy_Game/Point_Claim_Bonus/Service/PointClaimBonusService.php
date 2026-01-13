<?php

namespace App\Domain\Rummy_Game\Point_Claim_Bonus\Service;

//Data
use App\Domain\Rummy_Game\Point_Claim_Bonus\Data\PointClaimBonusData;
use App\Domain\Rummy_Game\Point_Claim_Bonus\Data\PointClaimBonusDataRead;


//Repository
use App\Domain\Rummy_Game\Point_Claim_Bonus\Repository\PointClaimBonusRepository;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;


final class PointClaimBonusService
{
    private PointClaimBonusRepository $repository;
    private LoggerInterface $logger;



    public function __construct(PointClaimBonusRepository $repository, LoggerFactory $loggerFactory)
    {
        $this->repository = $repository;
        

        $this->logger = $loggerFactory->addFileHandler('Rummy_Game/Point_Claim_Bonus/PointClaimBonus.log')->createLogger();
    }

    public function PointClaimBonus(array $data, int $user_id): array
    { 
        
        $claim = $this->repository->PointClaimBonus($data, $user_id);

        return $claim;
    }

 
   

}
