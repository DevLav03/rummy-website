<?php

namespace App\Domain\Master_Table\Master_Game_Country\Service;

//Data
use App\Domain\Master_Table\Master_Game_Country\Data\CountryData;
use App\Domain\Master_Table\Master_Game_Country\Data\CountryDataRead;

//Validator
use App\Domain\Master_Table\Master_Game_Country\Validator\CountryCreateValidator;
use App\Domain\Master_Table\Master_Game_Country\Validator\CountryUpdateValidator;

//Repository
use App\Domain\Master_Table\Master_Game_Country\Repository\CountryRepository;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;


final class CountryService
{
    private CountryRepository $repository;
    private CountryCreateValidator $createValidator;
    private CountryUpdateValidator $updateValidator;

    private LoggerInterface $logger;

    public function __construct(CountryRepository $repository, LoggerFactory $loggerFactory, CountryCreateValidator $createValidator, CountryUpdateValidator $updateValidator)
    {
        $this->repository = $repository;
        $this->createValidator = $createValidator;
        $this->updateValidator = $updateValidator;

        $this->logger = $loggerFactory->addFileHandler('Master_Table/Master_Game_Country/Countrty.log')->createLogger();
    }

    //Get All Data
    public function getCountry(): CountryData
    {
        $country = $this->repository->getCountry();

        $result = new CountryData();

        foreach ($country as $countryRow) {
            $country = new CountryDataRead();
            $country->id = $countryRow['id'];
            $country->country_code = $countryRow['country_code'];
            $country->country_name = $countryRow['country_name'];
           

            $result->country[] = $country;
        }

        return $result;
       
    }

    //Get One Data
    public function getOneCountry(int $countryId): CountryData
    {
        $country = $this->repository->getOneCountry($countryId);

        $result = new CountryData();

        foreach ($country as $countryRow) {
            $country = new CountryDataRead();
            $country->id = $countryRow['id'];
            $country->country_code = $countryRow['country_code'];
            $country->country_name = $countryRow['country_name'];
           
            $result->country[] = $country;
        }

        return $result;
    }

    //Insert Data
    public function insertCountry(array $data): int
    {
        
        $this->createValidator->validateCreateData($data);

        $countryId = $this->repository->insertCountry($data);

        $this->logger->info(sprintf('Country created successfully: %s', $countryId));

        return $countryId;
    }



    //Update Data
    public function updateCountry(int $countryid, array $data): int
    { 
        $this->updateValidator->validateUpdateData($data);

        $country = $this->repository->updateCountry($countryid, $data);

        $this->logger->info(sprintf('Country updated successfully: %s', $countryid));

        return $country;
    }

  

    //Delete Data
    public function deleteCountry(int $countryId): int
    {
        $country = $this->repository->deleteCountry($countryId);

        $this->logger->info(sprintf('Country delete successfully: %s', $countryId));

        return $country;
    }

   

}
