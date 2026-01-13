<?php

namespace App\Domain\Rummy_Website\SocialMedia\Service;

//Data
use App\Domain\Rummy_Website\SocialMedia\Data\SocialMediaData;
use App\Domain\Rummy_Website\SocialMedia\Data\SocialMediaDataRead;

//Repository
use App\Domain\Rummy_Website\SocialMedia\Repository\SocialMediaRepository;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;


final class SocialMediaService
{
    private SocialMediaRepository $repository;
    private LoggerInterface $logger;

    public function __construct(SocialMediaRepository $repository,  LoggerFactory $loggerFactory)
    {
        $this->repository = $repository;
        $this->logger = $loggerFactory->addFileHandler('Rummy_Website/SocialMedia/SocialMedia.log')->createLogger();
    }

    //Get All Data
    public function getSocialMedia(): SocialMediaData
    {
        $socialmedia = $this->repository->getSocialMedia();

        $result = new SocialMediaData();

        foreach ($socialmedia as $socialmediaRow) {
            $socialmedia = new SocialMediaDataRead();
            $socialmedia->facebook = $socialmediaRow['facebook'];
            $socialmedia->google = $socialmediaRow['google'];
            $socialmedia->playstore = $socialmediaRow['playstore'];
            $socialmedia->android = $socialmediaRow['android'];
            $socialmedia->ios = $socialmediaRow['ios'];
        

            $result->socialmedia[] = $socialmedia;
        }

        return $result;
       
    }


    //Update Data
    public function updateSocialMedia( array $data): int
    { 
      

        $socialmedia = $this->repository->updateSocialMedia( $data);

        $this->logger->info(sprintf('SocialMedia updated successfully: %s', $data));

        return $socialmedia;
    }
}