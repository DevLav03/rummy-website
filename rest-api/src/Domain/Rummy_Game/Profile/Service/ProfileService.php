<?php

namespace App\Domain\Rummy_Game\Profile\Service;

//Validation
use App\Domain\Rummy_Game\Profile\Validator\ProfileUpdateValidator;
use App\Domain\Rummy_Game\Profile\Validator\ChangePasswordValidator;
//use App\Domain\Rummy_Game\Profile\Validator\imageVerifyValidator;

//Service
use App\Service\Password\PasswordService;

//Respository
use App\Domain\Rummy_Game\Profile\Repository\ProfileRepository;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;
use \Gumlet\ImageResize;


final class ProfileService
{
    private ProfileRepository $repository;
    private LoggerInterface $logger;
    private ProfileUpdateValidator $profileUpdateValidator;
    private ChangePasswordValidator $changePasswordValidator;
    private PasswordService $passwordService;


    public function __construct(ProfileRepository $repository,  LoggerFactory $loggerFactory, PasswordService $passwordService, ProfileUpdateValidator $profileUpdateValidator, ChangePasswordValidator $changePasswordValidator)
    {
        $this->repository = $repository;
        $this->profileUpdateValidator = $profileUpdateValidator;
        $this->changePasswordValidator = $changePasswordValidator;
        $this->passwordService = $passwordService;
        
        $this->logger = $loggerFactory->addFileHandler('Rummy_Game/Profile/profile.log')->createLogger();   
    }

    //update profile
    public function updateUserProfile(int $userId, array $data): int
    { 

        $this->profileUpdateValidator->validateData($data);

        $admin = $this->repository->updateUserProfile($userId, $data);

        $this->logger->info(sprintf('Profile updated successfully: %s', $userId));

        return $admin;
    }
  

   
    //Change Password
    public function changePassword(int $userId, array $data): string
    {
        $this->changePasswordValidator->validateData($data);

        $ret = $this->validatePasswords($userId, $data);

        $password = $this->passwordService->passwordEncrytion($data['new_password']);
       
        if(empty($ret)){
            $res = $this->repository->changePassword($userId, $password);
            return  "success";
        }else{
            return  (string)$ret;
        }
      
    }
        
    //Validate Password
    public function validatePasswords(int $userId, array $data): int
    {
       
        $old_password = $this->passwordService->passwordEncrytion($data['old_password']);
        $new_password = $this->passwordService->passwordEncrytion($data['new_password']);
        $re_new_password = $this->passwordService->passwordEncrytion($data['re_new_password']);

        if($this->checkOldPassword($userId , $old_password) == true){

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

    public function checkOldPassword($userId , $old_password){
        $result = $this->repository->getCurrentPassword($userId, $old_password);
        return count($result)>0?true:false;
    }

    //Insert
    public function uploadProfileImage(array $data, int $user_id, $uploadedFile): int
    {


        //File Uploads
        try{
           // $sourceProperties = getimagesize($uploadedFile);
            //var_dump($uploadedFile);exit;
           // $base64_image=base64_encode(file_get_contents($uploadedFile));
           // var_dump($base64_image);exit;
            // $src = imagecreatefromstring(file_get_contents($uploadedFile));
            // $b64 = base64_encode($src);

            // // Show the Base64 value
            // echo $b64;exit;
            // var_dump($src);exit;
            $directory =  "../uploads/user_profile/";
            $small_directory="../uploads/user_profile/small/";
            $medium_directory="../uploads/user_profile/medium/";
            $large_directory="../uploads/user_profile/large/";
            $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
            $size = $uploadedFile->getSize();
            // $sizevalid = $uploadedFile-getSize();
            // print_r($size); exit;
            // var_dump($uploadedFile);exit;
            $basename = bin2hex(random_bytes(8));
            $filename = sprintf('%s.%0.8s', $basename, $extension); 

            
            // if($width == 400 & $height == 400){

            // }else{

            // }
           

            if($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg'){
                if($size <= 2097152){
                    if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                        $uploadFileName = $uploadedFile->getClientFilename();
                        $uploadedFile->moveTo($directory .$filename);
                        $img_size_array = getimagesize($directory .$filename);
                       
                        $width = $img_size_array[0];
                        $height = $img_size_array[1];
                        //use size validation here
                        $image = new ImageResize($directory .$filename);
                        $image->resizeToBestFit(50, 50);
                        $image->save($small_directory.$filename);
                        $image->resizeToBestFit(100, 100);
                        $image->save($medium_directory.$filename);
                        $image->resizeToBestFit(150, 150);
                        $image->save($large_directory.$filename);
                        $profileId = $this->repository->uploadProfileImage($user_id, $filename); 
                        $this->logger->info(sprintf('Profile Image  request id: %s', $profileId));

                        return 0;
                    }else{
                        return 1;
                    }
                }else{
                    return 2;
                } 
            }else{
                return 3;
            }          
        }catch(Exception $ex){
            var_dump($ex);
        }
        
    }
  
}
