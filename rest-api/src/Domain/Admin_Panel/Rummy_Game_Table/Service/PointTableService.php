<?php

namespace App\Domain\Admin_Panel\Rummy_Game_Table\Service;

//Data
// use App\Domain\Admin_Panel\Rummy_Game_Table\Data\PointTableData;
// use App\Domain\Admin_Panel\Rummy_Game_Table\Data\PointTableDataRead;

//Validator
use App\Domain\Admin_Panel\Rummy_Game_Table\Validator\PointTableValidator;

//Repository
use App\Domain\Admin_Panel\Rummy_Game_Table\Repository\PointTableRepository;


final class PointTableService
{
    private PointTableRepository $repository;
    private PointTableValidator $pointTableValidator;

    public function __construct(PointTableRepository $repository, PointTableValidator $pointTableValidator)
    {
        $this->repository = $repository;
        $this->pointTableValidator = $pointTableValidator;
    }

    //Point Rummy 
    public function getPointrummy(array $data): array //PointTableData
    {

        //$this->pointTableValidator->validateData($data);

        $point_rummy = $this->repository->getPointrummy($data);

        // $result = new PointTableData();

        // foreach ($point_rummy as $pointRow) {

        //     $point = new PointTableDataRead();

        //     $point->total = $pointRow['total'];
        //     $point->id = $pointRow['id'];
        //     $point->game = $pointRow['game'];
        //     $point->game_type_id = $pointRow['game_type_id'];
        //     $point->table_name = $pointRow['table_name'];
        //     $point->table_no = $pointRow['table_no'];
        //     $point->bet_value = $pointRow['bet_value'];
        //     $point->point_value = $pointRow['point_value'];
        //     $point->sitting_capacity = $pointRow['sitting_capacity'];
        //     $point->game_deck = $pointRow['game_deck'];
        //     $point->status = $pointRow['status'];
        //     $point->table_status = $pointRow['table_status'];
        //     $point->created_at = $pointRow['created_at'];

        //     $result->real_money_tables[] = $point;
        // }

        //return $result;

        return $point_rummy;

    }
 
    
    //Pool Rummy 
    public function getPoolrummy(array $data): array
    {
        $pool_rummy = $this->repository->getPoolrummy($data);
        return  $pool_rummy ;   
    }


    //deal Rummy 
    public function getDealrummy(array $data): array
    {
        $deal_rummy = $this->repository->getDealrummy($data);
        return  $deal_rummy ;   
    }
    
    //Do or Die Rummy 
    public function getDoDierummy(array $data): array
    {
        $dodie_rummy = $this->repository->getDoDierummy($data);
        return  $dodie_rummy ;   
    }
 
    
   
}
