<?php

namespace App\Domain\Rummy_Website\Settings\Service;



//Data
use App\Domain\Rummy_Website\Settings\Data\SettingsData;
use App\Domain\Rummy_Website\Settings\Data\SettingsDataRead;

//Validator
// use App\Domain\Rummy_Website\Our_Games\Validator\OurGamesCreateValidator;
// use App\Domain\Rummy_Website\Our_Games\Validator\OurGamesUpdateValidator;
use App\Service\Image\ImageService;

//Repository
use App\Domain\Rummy_Website\Settings\Repository\SettingsRepository;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;
use Exception;



final class SettingsService
{
    private SettingsRepository $repository;
    private ImageService $imageService;

    // private OurGamesCreateValidator $createValidator;
    // private OurGamesUpdateValidator $updateValidator ,OurGamesCreateValidator $createValidator, OurGamesUpdateValidator $updateValidator;

    private LoggerInterface $logger;

    public function __construct(SettingsRepository $repository,  LoggerFactory $loggerFactory, ImageService $imageService)
    {
        $this->repository = $repository;
        $this->imageService = $imageService;

        // $this->createValidator = $createValidator;
        // $this->updateValidator = $updateValidator;

        $this->logger = $loggerFactory->addFileHandler('Rummy_Website/Settings/Settings.log')->createLogger();
    }

    //Get All Data
    public function getLogoSettings(): SettingsData
    {
        $settings = $this->repository->getLogoSettings();
       
        //Image convert to string
        $img_file = '../uploads/website/settings/'.$settings[0]['logo_image'];
        $image = $this->imageService->imageString($img_file);
        // print_r($image); exit;
    
        $result = new SettingsData();

        foreach ($settings as $settingsRow) {
            $settings = new SettingsDataRead();
            $settings->logo_image = $image;
            $settings->footer = $settingsRow['footer'];
            $settings->banner_title = $settingsRow['banner_title'];
           

            $result->settings[] = $settings;
        }

        return $result;
       
    }

    //Update
    public function updateLogoSettings(array $data, $logo_img ): int //$banner_logo
    {
    
       //File Uploads 
        try{

           
            if(!empty($logo_img)){

                //print_r('full data'); exit;

                $setting_path =  "../uploads/website/settings/";
                $uploadFileLogoName = $logo_img->getClientFilename();
                $basename = bin2hex(random_bytes(8));
                $logo_extension = pathinfo($logo_img->getClientFilename(), PATHINFO_EXTENSION);
                $logo_size = $logo_img->getSize();
                $logo_filename = sprintf('%s.%0.8s', 'logo', $logo_extension);
            
    
                if($logo_extension == 'png' || $logo_extension == 'jpg' || $logo_extension == 'jpeg' || $logo_extension == ''){
                    if($logo_size <= 2097152){
                        if($logo_img->getError() === UPLOAD_ERR_OK) {
                            $logo_img->moveTo($setting_path .$logo_filename);
                            $settingsId = $this->repository->updateLogoSettings($data, $logo_filename);
                            //print_r( $settingsId); exit;
                            return $settingsId;
                        }else{
                            return 31;
                        } 
                    }else{
                        return 21;
                    } 
                }else{
                    return 11;
                }
            }else{
                $settingsId = $this->repository->updateLogoSettings($data, ''); 
                return $settingsId;
            }         
       }catch(Exception $ex){
           var_dump($ex);
       }    
    }

}
