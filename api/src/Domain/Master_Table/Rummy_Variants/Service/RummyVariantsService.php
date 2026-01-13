<?php

namespace App\Domain\Master_Table\Rummy_Variants\Service;

//Data
use App\Domain\Master_Table\Rummy_Variants\Data\RummyVariantsData;
use App\Domain\Master_Table\Rummy_Variants\Data\RummyVariantsDataRead;

//Repository
use App\Domain\Master_Table\Rummy_Variants\Repository\RummyVariantsRepository;

//Validator
use App\Domain\Master_Table\Rummy_Variants\Validator\RummyVariantsCreateValidator;
use App\Domain\Master_Table\Rummy_Variants\Validator\RummyVariantsUpdateValidator;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;

final class RummyVariantsService
{
    private RummyVariantsRepository $repository;
    private RummyVariantsCreateValidator $createValidator;
    private RummyVariantsUpdateValidator $updateValidator;

    private LoggerInterface $logger;

    //

    public function __construct(RummyVariantsRepository $repository, LoggerFactory $loggerFactory, RummyVariantsCreateValidator $createValidator, RummyVariantsUpdateValidator $updateValidator) 
    {
        $this->repository = $repository;
        $this->createValidator = $createValidator;
        $this->updateValidator = $updateValidator;

        $this->logger = $loggerFactory->addFileHandler('Master_Table/Rummy_Variants/Rummy_Variants.log')->createLogger();
    }

    //Get All Data
    public function getRummyVariants(): RummyVariantsData
    {
        $variants = $this->repository->getRummyVariants();

        $result = new RummyVariantsData();

        foreach ($variants as $variantsRow) {
            $variant = new RummyVariantsDataRead();
            $variant->id = $variantsRow['id'];
            $variant->name = $variantsRow['name'];
            $variant->description = $variantsRow['description'];
            $variant->active = $variantsRow['active'];
            $variant->created_at = $variantsRow['created_at'];

            $result->variants[] = $variant;
        }

        return $result;
       
    }

    //Get Active Data
    public function getActiveRummyVariants(): RummyVariantsData
    {
        $variants = $this->repository->getActiveRummyVariants();

        $result = new RummyVariantsData();

        foreach ($variants as $variantsRow) {
            $variant = new RummyVariantsDataRead();
            $variant->id = $variantsRow['id'];
            $variant->name = $variantsRow['name'];
            $variant->description = $variantsRow['description'];
            $variant->active = $variantsRow['active'];
            $variant->created_at = $variantsRow['created_at'];

            $result->variants[] = $variant;
        }

        return $result;
       
    }


    //Insert Data
    public function insertRummyVariants(array $data): int
    {
        
        $this->createValidator->validateCreateData($data);

        $variantId = $this->repository->insertRummyVariants($data);

        $this->logger->info(sprintf('Rummy Variants created successfully: %s', $variantId));

        return $variantId;
    }

    //Update Data
    public function updateRummyVariants(int $variantId, array $data): int
    { 
        $this->updateValidator->validateUpdateData($data);

        $variants = $this->repository->updateRummyVariants($variantId, $data);

        $this->logger->info(sprintf('Rummy Variants updated successfully: %s', $variantId));

        return $variants;
    }

    //Remove Admin IP Restrict Status
    public function rummyVariantStatus(int $variantId, int $status): int
    {
        $variants = $this->repository->rummyVariantStatus($variantId, $status);

        return  $variants; 
    }

    //Delete Data
    public function deleteRummyVariants(int $variantId): int
    {
        $variants = $this->repository->deleteRummyVariants($variantId);

        $this->logger->info(sprintf('Rummy Variants delete successfully: %s', $variantId));

        return $variants;
    }
}
