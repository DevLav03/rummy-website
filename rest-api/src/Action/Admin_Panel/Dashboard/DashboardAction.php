<?php

namespace App\Action\Admin_Panel\Dashboard;

//Service
use App\Domain\Admin_Panel\Dashboard\Service\DashboardService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class DashboardAction
{
    private DashboardService $dashboardService;

    private JsonRenderer $renderer;
    
    public function __construct(DashboardService $dashboardService, JsonRenderer $jsonRenderer)
    {
        $this->dashboardService = $dashboardService;
        $this->renderer = $jsonRenderer;
    }

    //Get Data
    public function getAdminCount(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {

        $admins = $this->dashboardService->getAdminCount();

        $ret=array("response"=>"success", "data"=> $admins);
      
        return $this->renderer->json($response, $ret);
    }

    //Get Data
    public function getCareerCount(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {

        $admins = $this->dashboardService->getCareerCount();

        $ret=array("response"=>"success", "data"=> $admins);
      
        return $this->renderer->json($response, $ret);
    }

    //Get Data
    public function getProjectCount(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {

        $admins = $this->dashboardService->getProjectCount();

        $ret=array("response"=>"success", "data"=> $admins);
      
        return $this->renderer->json($response, $ret);
    }

    //Get Data
    public function getContactCount(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {

        $admins = $this->dashboardService->getContactCount();

        $ret=array("response"=>"success", "data"=> $admins);
      
        return $this->renderer->json($response, $ret);
    }

    //Get Data
    public function getJobPostCount(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {

        $admins = $this->dashboardService->getJobPostCount();

        $ret=array("response"=>"success", "data"=> $admins);
      
        return $this->renderer->json($response, $ret);
    }


    public function getCareerYear(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
        $career_year = $this->dashboardService->careerYear();

        $ret = array("response"=>"success", "data"=> $career_year);
      
        return $this->renderer->json($response, $ret);
    }

    public function getCareerMonth(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
        $career_year = $this->dashboardService->careerMonth();

        $ret = array("response"=>"success", "data"=> $career_year);
      
        return $this->renderer->json($response, $ret);
    }
}
