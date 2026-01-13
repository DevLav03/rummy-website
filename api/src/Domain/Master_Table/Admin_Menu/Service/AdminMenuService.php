<?php

namespace App\Domain\Master_Table\Admin_Menu\Service;

//Data
use App\Domain\Master_Table\Admin_Menu\Data\AdminMenuData;
use App\Domain\Master_Table\Admin_Menu\Data\AdminMenuDataRead;

//Validator
use App\Domain\Master_Table\Admin_Menu\Validator\AdminMenuCreateValidator;
use App\Domain\Master_Table\Admin_Menu\Validator\AdminMenuUpdateValidator;

//Repository
use App\Domain\Master_Table\Admin_Menu\Repository\AdminMenuRepository;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;


final class AdminMenuService
{
    private AdminMenuRepository $repository;
    private AdminMenuCreateValidator $createValidator;
    private AdminMenuUpdateValidator $updateValidator;

    private LoggerInterface $logger;

    public function __construct(AdminMenuRepository $repository, LoggerFactory $loggerFactory, AdminMenuCreateValidator $createValidator, AdminMenuUpdateValidator $updateValidator) // 
    {
        $this->repository = $repository;
        $this->createValidator = $createValidator;
        $this->updateValidator = $updateValidator;

        $this->logger = $loggerFactory->addFileHandler('Master_Table/Admin_Menu/AdminMenu.log')->createLogger();
    }

    //Get All Data
    public function getAllMenu(): array
    {
        $menu = $this->repository->getAllMenu($data);
    
        return $menu;
        
    }

    public function getMenu(array $data): array
    {
        $menu_rows = $this->repository->getMenu($data);
        $result=$this->manipulate_menu($menu_rows);
        return $result;
        
    }

    private function manipulate_menu($menu_list){
        $final_menu_array=array();
        foreach($menu_list as $menu => $val) {
            if(!empty($val['parent_id'])){
                $parent_menu_exists=$this->check_parent_menu_exists($final_menu_array,$val['parent_id']);
                if(!$parent_menu_exists){

                }
            }
            $this_menu=array("path"=>$val['menu_link'],"title"=>$val['menu_name'],"id"=>$val['id'],"icon"=>$val['icons'],"sub_menu"=>[]);
            $final_menu_array[]=$this_menu;
            //echo $menu; echo $val['menu_name'];
          }
        //print_r($final_menu_array);
       // print_r($menu_list);exit;
        return $final_menu_array;
    }

    private function check_parent_menu_exists($final_menu_array,$parent_id){
        foreach($menu_list as $menu => $val) {
            if($val['parent_id'] == $parent_id){
                $parent_menu_exists=true;
                return true;
            }
        }
        return false;
    }

    //Update Data
    public function updateMenu(int $menuId, array $data): int
    { 
        $this->updateValidator->validateUpdateData($data);

        $menu = $this->repository->updateMenu($menuId, $data);

        $this->logger->info(sprintf('AdminMenu updated successfully: %s', $menuId));

        return $menu;
    }
   
    //Status Change
    public function menuStatus(int $menuId, int $status): int
    { 
        $menu = $this->repository->menuStatus($menuId, $status);

        //print_r($menu); exit;

        $this->logger->info(sprintf('Active status updated successfully: %s', $menuId));

        return $menu;
    }
   

}
