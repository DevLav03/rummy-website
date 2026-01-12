<?php

namespace App\Domain\Admin_Panel\Users\Service;

//Data
use App\Domain\Admin_Panel\Users\Data\UsersData;
use App\Domain\Admin_Panel\Users\Data\UsersDataRead;

use App\Domain\Admin_Panel\Users\Data\UserLogData;
use App\Domain\Admin_Panel\Users\Data\UserLogDataRead;

//Image Service
use App\Service\Image\ImageService;
use \Gumlet\ImageResize;


//Validation
use App\Domain\Admin_Panel\Users\Validator\LogHistoryValidator;

//Repository
use App\Domain\Admin_Panel\Users\Repository\UsersRepository;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;
use UAParser\Parser;

final class UsersService
{
    private UsersRepository $repository;
    private LogHistoryValidator $logHistoryValidator;
    private ImageService $imageService;
    private LoggerInterface $logger;

    public function __construct(UsersRepository $repository, LoggerFactory $loggerFactory, ImageService $imageService, LogHistoryValidator $logHistoryValidator)
    {
        $this->repository = $repository;
        $this->logHistoryValidator = $logHistoryValidator;
        $this->imageService = $imageService;
        $this->logger = $loggerFactory->addFileHandler('Admin_Panel/Users/Users.log')->createLogger();
    }

    //Get All Data
    public function getUsers():UsersData
    {
        $users = $this->repository->getUsers();

       
      
        $result = new UsersData();

        foreach ($users as $userRow) {
            
            $user = new UsersDataRead();

           
            //print_r($userRow['profile_image']);exit;
            if($userRow['profile_image'] == null){
                $image = '';
            }else{
                $img_file = '../uploads/user_profile/'.$userRow['profile_image'];
                $image = $this->imageService->imageString($img_file);
                // $imagesize = ImageResize::createFromString(base64_decode('R0lGODlhAQABAIAAAAQCBP///yH5BAEAAAEALAAAAAABAAEAAAICRAEAOw=='));
                // $imagesize->scale(50);
                // $imagesize->save('image.jpg');

            }


            $user->id = $userRow['id'];
            $user->name = $userRow['name'];
            $user->email = $userRow['email'];
            $user->phone_no = $userRow['phone_no'];
            $user->username = $userRow['username'];
            $user->active = $userRow['active'];
            $user->timezone = $userRow['timezone'];
            $user->timezonevalue = $userRow['timezonevalue'];
            $user->comments = $userRow['comments'];
            $user->disabled_date = $userRow['disabled_date'];
            $user->disabled_admin = $userRow['disabled_admin'];
            $user->profile_completed = $userRow['profile_completed'];
            $user->cash_bonus = $userRow['cash_bonus'];
            $user->ip_registration = $userRow['ip_registration'];
            $user->registered_date_time = $userRow['registered_date_time'];
            $user->allow_cash_bonus = $userRow['allow_cash_bonus'];
            $user->premium_flag = $userRow['premium_flag'];
            $user->online_status = $userRow['online_status'];
            $user->profile_image = $image;
            $user->reg_type = $userRow['reg_type'];
            $user->refer_code = $userRow['refer_code'];
          
            $result->users[] = $user;
        }

        return $result;

    }

    //Get One Data
    public function getOneUser(int $userId): UsersData
    {
        // print_r($userId); exit;
        $users = $this->repository->getOneUser($userId); 

        

        $result = new UsersData();

        foreach ($users as $userRow) {

            if($userRow['profile_image'] == null){
                $image = '';
            }else{
                $img_file = '../uploads/user_profile/'.$userRow['profile_image'];
                $image = $this->imageService->imageString($img_file);

            }            

            $user = new UsersDataRead();
            $user->id = $userRow['id'];
            $user->name = $userRow['name'];
            $user->last_name = $userRow['last_name'];
            $user->username = $userRow['username'];
            $user->email = $userRow['email'];
            $user->email_verify_status = $userRow['email_verify_status'];
            $user->phone_no = $userRow['phone_no'];
            $user->phone_verify_status = $userRow['phone_verify_status'];
            $user->gender = $userRow['gender'];
            $user->dateofbirth = $userRow['dateofbirth'];
            $user->state = $userRow['state'];
            $user->city = $userRow['city'];
            $user->pin_code = $userRow['pin_code'];
            $user->active = $userRow['active'];
            $user->online_status = $userRow['online_status'];
            $user->premium_flag = $userRow['premium_flag'];
            $user->user_tier_level = $userRow['user_tier_level'];
            $user->user_star_level = $userRow['user_star_level'];
            $user->user_rank_level = $userRow['user_rank_level'];
            $user->tier_name = $userRow['tier_name'];
            $user->last_action_time = $userRow['last_action_time'];
            $user->created_at = $userRow['created_at'];
            $user->profile_image = $image;
          
            $result->users[] = $user;
        }

        return $result;
    }


    //Get Cash Chips
    public function getCashChips(int $userId): array
    {
        $users = $this->repository->getCashChips($userId);

        return $users;
    }

    //Get Free Chips
    public function getFreeChips(int $userId): array
    {
        $users = $this->repository->getFreeChips($userId);

        return $users;
    }

    //Get Bonus
    public function getBonus(int $userId): array
    {
        $users = $this->repository->getBonus($userId);

        return $users;
    }

    //Get Points
    public function getPoints(int $userId): array
    {
        $users = $this->repository->getPoints($userId);

        return $users;
    }

    //Get Points
    public function usersGameDetails(int $userId): array
    {
        $users = $this->repository->usersGameDetails($userId);

        return $users;
    }


    //Block and Unblock
    public function activeUser(int $user_id, int $active): int
    {
        $user = $this->repository->activeUser($user_id, $active);

        $this->logger->info(sprintf('Blocked or Unblocked Successfully: %s', $user_id));

        return $user;
    }

    
    //Users Log History 
    public function userLogHistory(array $data, int $user_id): UserLogData
    {

        $this->logHistoryValidator->validateGetData($data); 

        $users = $this->repository->userLogHistory($data, $user_id);

        $user_mod = $this->parseUserAgent($users);

        $result = new UserLogData();

        
        foreach ($user_mod as $userRow) {
            $user = new UserLogDataRead();
            $user->id = $userRow['id'];
            $user->user_id = $userRow['user_id'];
            $user->login_device = $userRow['login_device'];
            $user->user_country_name = $userRow['country_name'];
            $user->user_state_name = $userRow['state_name'];
            $user->user_city_name = $userRow['city_name'];
            $user->location_ip = $userRow['location_ip'];
            $user->action = $userRow['action'];
            $user->created_at = $userRow['created_at'];
          
            $result->user_log[] = $user;
        }

        return $result;
    
    }
    public function parseUserAgent(& $fields){
        $i=0;
        $parser = Parser::create();
        foreach($fields as $field){
            if($field['login_device']){
                $result = $parser->parse("Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Safari/537.36");
                $fields[$i]['login_device'] =  $result->toString();   
            }
            $i++;
        }
        return $fields;
    }

    public function userLogHistoryCount(array $data, int $user_id): int
    {
        $total = $this->repository->userLogHistoryCount($data, $user_id);
        return  $total; 
    }
 

}
