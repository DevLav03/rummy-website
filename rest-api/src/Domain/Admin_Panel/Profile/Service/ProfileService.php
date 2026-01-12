<?php

namespace App\Domain\Admin_Panel\Profile\Service;

//Validation
use App\Domain\Admin_Panel\Profile\Validator\ProfileUpdateValidator;
use App\Domain\Admin_Panel\Profile\Validator\ChangePasswordValidator;

//Service
use App\Service\Password\PasswordService;

//Repository
use App\Domain\Admin_Panel\Profile\Repository\ProfileRepository;
 
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;

/**
 * Service.
 */
final class ProfileService
{
    private ProfileRepository $repository;
    private ProfileUpdateValidator $profileUpdateValidator;
    private ChangePasswordValidator $changePasswordValidator;
    private PasswordService $passwordService;
  
    private LoggerInterface $logger;

    public function __construct(ProfileRepository $repository, ProfileUpdateValidator $profileUpdateValidator, LoggerFactory $loggerFactory, PasswordService $passwordService, ChangePasswordValidator $changePasswordValidator)
    {
        $this->repository = $repository;
        $this->profileUpdateValidator = $profileUpdateValidator;
        $this->changePasswordValidator = $changePasswordValidator;
        $this->passwordService = $passwordService;

        $this->logger = $loggerFactory->addFileHandler('Admin_Panel/Profile/Admin_Profile.log')->createLogger();
    }    

    //update profile
    public function updateProfile(int $adminId, array $data): int
    { 

        $this->profileUpdateValidator->validateUpdateData($data);

        $admin = $this->repository->updateProfile($adminId, $data);

        $this->logger->info(sprintf('Profile updated successfully: %s', $adminId));

        return $admin;
    }

    //Update Password
    public function updatePassword(int $adminId, array $data): string
    {
        $this->changePasswordValidator->validateUpdateData($data);

        $ret = $this->validatePasswords($adminId, $data);

        $password = $this->passwordService->passwordEncrytion($data['new_password']);
       
        if(empty($ret)){
            $res = $this->repository->updatePassword($adminId, $password);
            return  "success";
        }else{
            return  (string)$ret;
        }
      
    }

    //Validate Password
    public function validatePasswords(int $adminId, array $data): int
    {
        $old_password = $this->passwordService->passwordEncrytion($data['old_password']);
        $new_password = $this->passwordService->passwordEncrytion($data['new_password']);
        $re_new_password = $this->passwordService->passwordEncrytion($data['re_new_password']);

        if($this->checkOldPassword($adminId , $old_password) == true){

            if($old_password != $new_password){
                if($new_password != $re_new_password){
                    return 103; //new password and rep new password are not same
                }
            }else{
                return 102; //old password and new password are same
            }
            
        }else{
            return 101; //invalid old password
        }
        return 0;
    }

    public function checkOldPassword($adminId , $old_password){

        $result = $this->repository->getCurrentPassword($adminId, $old_password);
        return count($result)>0?true:false;

    }
}