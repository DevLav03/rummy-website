<?php

namespace App\Domain\Rummy_Game\Leaderboard\Service;

//Data
use App\Domain\Rummy_Game\Leaderboard\Data\LeaderboardData;
use App\Domain\Rummy_Game\Leaderboard\Data\LeaderboardDataRead;
use App\Domain\Rummy_Game\Leaderboard\Data\ReferearnLeaderboardData;
use App\Domain\Rummy_Game\Leaderboard\Data\ReferearnLeaderboardDataRead;

//Repository
use App\Domain\Rummy_Game\Leaderboard\Repository\LeaderboardRepository;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;


final class LeaderboardService
{
    private LeaderboardRepository $repository;
    private LoggerInterface $logger;



    public function __construct(LeaderboardRepository $repository, LoggerFactory $loggerFactory)
    {
        $this->repository = $repository;
        

        $this->logger = $loggerFactory->addFileHandler('Rummy_Game/Leaderboard/Leaderboards.log')->createLogger();
    }


    //leaderboard top 15 players

    public function getTop15Players(): LeaderboardData
    {
        $leaderboard = $this->repository->getTop15Players();

        $result = new LeaderboardData();

        foreach ($leaderboard as $leaderboardRow) {
            $leaderboard = new LeaderboardDataRead();
            $leaderboard->id = $leaderboardRow['id'];
            //$leaderboard->user_id = $leaderboardRow['user_id'];
            $leaderboard->total_point = $leaderboardRow['total_point'];
            // $leaderboard->point_decrease = $leaderboardRow['point_decrease'];
            //$leaderboard->point_inhand = $leaderboardRow['point_inhand'];
           

            $result->leaderboard[] = $leaderboard;
        }

        return $result;
       
    }

    //refer & earn top 15
    public function referearnTop15Players(): ReferearnLeaderboardData
    {
        $referearnleaderboard = $this->repository->referearnTop15Players();

        $result = new ReferearnLeaderboardData();

        foreach ($referearnleaderboard as $referearnleaderboardRow) {
            $referearnleaderboard = new ReferearnLeaderboardDataRead();
            $referearnleaderboard->id = $referearnleaderboardRow['id'];
            //$referearnleaderboard->user_id = $referearnleaderboardRow['user_id'];
            $referearnleaderboard->total_point = $referearnleaderboardRow['total_point'];
           // $referearnleaderboard->point_decrease = $referearnleaderboardRow['point_decrease'];
            //$referearnleaderboard->point_inhand = $referearnleaderboardRow['point_inhand'];
           

            $result->referearnleaderboard[] = $referearnleaderboard;
        }

        return $result;
       
    }
   

}
