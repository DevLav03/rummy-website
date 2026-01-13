<?php

namespace App\Action\Rummy_Game\Leaderboard;

//Service
use App\Domain\Rummy_Game\Leaderboard\Service\LeaderboardService;


use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class LeaderboardAction
{
    private LeaderboardService $service;
    private JsonRenderer $renderer;

    public function __construct(LeaderboardService $service, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }

    //leaderboard top 15 players
    public function getTop15Players(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
       
        $leaderboard = $this->service->getTop15Players();

        $ret=array("response"=>"success", "data"=>$leaderboard);
        
        return $this->renderer->json($response, $ret);

    }

    //refer & earn top 15
    public function referearnTop15Players(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
       
        $referearnleaderboard = $this->service->referearnTop15Players();

        $ret=array("response"=>"success", "data"=>$referearnleaderboard);
        
        return $this->renderer->json($response, $ret);

    }

}

