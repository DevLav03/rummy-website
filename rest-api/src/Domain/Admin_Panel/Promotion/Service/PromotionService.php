<?php

namespace App\Domain\Admin_Panel\Promotion\Service;

//Data
use App\Domain\Admin_Panel\Promotion\Data\PromotionData;
use App\Domain\Admin_Panel\Promotion\Data\PromotionDataRead;

//Validator
use App\Domain\Admin_Panel\Promotion\Validator\PromotionCreateValidator;
use App\Domain\Admin_Panel\Promotion\Validator\PromotionUpdateValidator;

//Repository
use App\Domain\Admin_Panel\Promotion\Repository\PromotionRepository;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;
use \Gumlet\ImageResize;

final class PromotionService
{
    private PromotionRepository $repository;
    //private PromotionCreateValidator $createValidator;
    //private PromotionUpdateValidator $updateValidator;

    private LoggerInterface $logger;

    public function __construct(PromotionRepository $repository, LoggerFactory $loggerFactory, ) //PromotionCreateValidator $createValidator, PromotionUpdateValidator $updateValidator
    {
        $this->repository = $repository;
        // $this->createValidator = $createValidator;
        // $this->updateValidator = $updateValidator;

        $this->logger = $loggerFactory->addFileHandler('Admin_Panel/Promotion/Promotions.log')->createLogger();
    }

    //Get All Data
    public function getPromotion(): PromotionData
    {
        $promotion = $this->repository->getPromotion();

        $result = new PromotionData();

        foreach ($promotion as $promotionRow) {
            $promotion = new PromotionDataRead();
            $promotion->id = $promotionRow['id'];
            $promotion->title = $promotionRow['title'];
            $promotion->short_description = $promotionRow['short_description'];
            $promotion->description = $promotionRow['description'];
            $promotion->promotion_image = $promotionRow['promotion_image'];
            $promotion->status = $promotionRow['status'];
            $promotion->created_at = $promotionRow['created_at'];

            $result->promotion[] = $promotion;
        }

        return $result;
       
    }


    //Get One Data
    public function getOnePromotion(int $promotionId): array
    {
        $promotion = $this->repository->getOnePromotion($promotionId);

        return $promotion;
    }
    
 
    //Insert
    public function insertPromotion(array $data, $uploadedFile): int //
    {
        //File Uploads
        try{
            $directory =  "../uploads/promotion_image/";
            $small_directory="../uploads/promotion_image/small/";
            $medium_directory="../uploads/promotion_image/medium/";
            $large_directory="../uploads/promotion_image/large/";
            $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
            $size = $uploadedFile->getSize();
            $basename = bin2hex(random_bytes(8));
            $filename = sprintf('%s.%0.8s', $basename, $extension); 

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
                        $promotionId = $this->repository->insertPromotion($data, $filename); 
                        $this->logger->info(sprintf('Promotion Image  request id: %s', $promotionId));

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

   //Update
   public function updatePromotion(array $data, int $promotion_id, $uploadedFile): int
   {
       //File Uploads
       try{
           $directory =  "../uploads/promotion_image/";
           $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
           $size = $uploadedFile->getSize();
           $basename = bin2hex(random_bytes(8));
           $filename = sprintf('%s.%0.8s', $basename, $extension); 

           if($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg'){
               if($size <= 2097152){
                   if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                       $uploadFileName = $uploadedFile->getClientFilename();
                       $uploadedFile->moveTo($directory .$filename);
       
                       $promotionId = $this->repository->updatePromotion($data,$promotion_id,  $filename); 
                       $this->logger->info(sprintf('Promotion Image  request id: %s', $promotionId));

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

    //Delete Data
    public function deletePromotion(int $promotion_Id): int
    {
        $promotion = $this->repository->deletePromotion($promotion_Id);

        $this->logger->info(sprintf('Promotion delete successfully: %s', $promotion_Id));

        return $promotion;
    }


    //Change Status
    public function promotionStatus(int $promotionId, int $status): int
    {
        $promotion = $this->repository->promotionStatusById($promotionId, $status);

        return  $promotion; 
    }
}
