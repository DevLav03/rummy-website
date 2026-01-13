<?php

namespace App\Domain\Admin_Panel\Dashboard\Service;

use App\Domain\Dashboard\Repository\DashboardRepository;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;

final class DashboardService
{
    private DashboardRepository $repository;

    private LoggerInterface $logger;

    public function __construct(DashboardRepository $repository)
    {
        $this->repository = $repository;
    }

    //Get All admin count
    public function getAdminCount()
    {
        $admins = $this->repository->getAdminCount();
      
        return $admins;
    }

    
    //Get All career count
    public function getCareerCount()
    {
        $career = $this->repository->getCareerCount();
      
        return $career;
    }

    //Get All Data
    public function getProjectCount()
    {
        $project = $this->repository->getProjectCount();
      
        return $project;
    }

    //Get All contact count
    public function getContactCount()
    {
        $contact = $this->repository->getContactCount();
      
        return $contact;
    }


    //Get All jobpost count
    public function getJobPostCount()
    {
        $posts = $this->repository->getJobPostCount();
      
        return $posts;
    }

    //get all  careeryear count
    public function careerYear()
    {
        $career_year = $this->repository->getCareerYear();
      
        return $career_year;
    }
   

    //get all careermonth count 
    public function careerMonth()
    {
        $career_month = $this->repository->getCareerMonth();
      
        return $career_month;
    }
}
