<?php

namespace App\Domain\Rummy_Website\Our_Games\Service;

//Data
use App\Domain\Rummy_Website\Our_Games\Data\OurGamesData;
use App\Domain\Rummy_Website\Our_Games\Data\OurGamesDataRead;

//Validator
// use App\Domain\Rummy_Website\Our_Games\Validator\OurGamesCreateValidator;
// use App\Domain\Rummy_Website\Our_Games\Validator\OurGamesUpdateValidator;

//Image Service
use App\Service\Image\ImageService;

//Repository
use App\Domain\Rummy_Website\Our_Games\Repository\OurGamesRepository;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;
use \Gumlet\ImageResize;


final class OurGamesService
{
    private OurGamesRepository $repository;
    private LoggerInterface $logger;
    // private OurGamesCreateValidator $createValidator;
    // private OurGamesUpdateValidator $updateValidator ,OurGamesCreateValidator $createValidator, OurGamesUpdateValidator $updateValidator;
    private ImageService $imageService;

    public function __construct(OurGamesRepository $repository,  ImageService $imageService, LoggerFactory $loggerFactory)
    {
        $this->repository = $repository;
        $this->imageService = $imageService;
        // $this->createValidator = $createValidator;
        // $this->updateValidator = $updateValidator;

        $this->logger = $loggerFactory->addFileHandler('Rummy_Website/Our_Games/OurGames.log')->createLogger();
    }

    //Get All Data
    public function getAllGames(): OurGamesData
    {
        $games = $this->repository->getAllGames();
        $result = new OurGamesData();

        foreach ($games as $gamesRow) {

            if($gamesRow['image'] == null){
                $image = '';
            }else{
                $img_file = '../uploads/website/our_games/'.$gamesRow['image'];
                $image = $this->imageService->imageString($img_file);

            }

            $games = new OurGamesDataRead();
            $games->id = $gamesRow['id'];
            $games->title = $gamesRow['title'];
            $games->type_heading = $gamesRow['type_heading'];
            $games->game_heading = $gamesRow['game_heading'];
            $games->image = $image;
            $games->status = $gamesRow['status'];
            $games->created_at = $gamesRow['created_at'];
        

            $result->games[] = $games;
        }

        return $result;
       
    }

    //Get All Data
    public function getGames(): OurGamesData
    {
        $games = $this->repository->getGames();

        // $img_file = '../uploads/website/our_games/'.$games[0]['image'];
        
        // $imgData = base64_encode(file_get_contents($img_file));

        
        // $src = 'data: '.mime_content_type($img_file).';base64,'.$imgData;

        $result = new OurGamesData();

        foreach ($games as $gamesRow) {

            if($gamesRow['image'] == null){
                $image = '';
            }else{
                $img_file = '../uploads/website/our_games/'.$gamesRow['image'];
                $image = $this->imageService->imageString($img_file);

            }

            $games = new OurGamesDataRead();
            $games->id = $gamesRow['id'];
            $games->title = $gamesRow['title'];
            $games->type_heading = $gamesRow['type_heading'];
            $games->game_heading = $gamesRow['game_heading'];
            $games->image = $image;
            $games->status = $gamesRow['status'];
            $games->created_at = $gamesRow['created_at'];
        

            $result->games[] = $games;
        }

        return $result;
       
    }

    //Insert
    public function insertGames(array $data, $uploadedFile): int
    {

        //var_dump($uploadedFile); exit;

        //File Uploads
        try{
            $directory =  "../uploads/website/our_games/";
            $small_directory="../uploads/website/our_games/small/";
            $medium_directory="../uploads/website/our_games/medium/";
            $large_directory="../uploads/website/our_games/large/";
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
                        $gamesId = $this->repository->insertGames($data, $filename); 
                        $this->logger->info(sprintf('Games Image  request id: %s', $gamesId));

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
    public function updateGames(array $data, int $games_id, $uploadedFile): int //
    {
       //File Uploads
       try{
           $directory =  "../uploads/website/games_image/";
           $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
           $size = $uploadedFile->getSize();
           $basename = bin2hex(random_bytes(8));
           $filename = sprintf('%s.%0.8s', $basename, $extension); 

           if($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg'){
               if($size <= 2097152){
                   if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                       $uploadFileName = $uploadedFile->getClientFilename();
                       $uploadedFile->moveTo($directory .$filename);
       
                       $gamesId = $this->repository->updateGames($data,$games_id,  $filename); 
                       $this->logger->info(sprintf('Games Image  request id: %s', $gamesId));

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

    //status change
    public function changeStatus(int $games_id, int $status): int
    {
        // print_r($admin_id); exit;

        $games = $this->repository->changeStatus($games_id, $status);

        $this->logger->info(sprintf('Blocked or Unblocked Successfully: %s', $games_id));

        return $games;
    }
  

    //Delete Data
    public function deleteGames(int $gamesId): int
    {
        $games = $this->repository->deleteGames($gamesId);

        $this->logger->info(sprintf('Games delete successfully: %s', $gamesId));

        return $games;
    }

   

}
