<?php

namespace App\Domain\Rummy_Website\News\Service;

//Data
use App\Domain\Rummy_Website\News\Data\NewsData;
use App\Domain\Rummy_Website\News\Data\NewsDataRead;

//Validator
// use App\Domain\Rummy_Website\Our_Games\Validator\OurGamesCreateValidator;
// use App\Domain\Rummy_Website\Our_Games\Validator\OurGamesUpdateValidator;

//Image Service
use App\Service\Image\ImageService;

//Repository
use App\Domain\Rummy_Website\News\Repository\NewsRepository;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;
use \Gumlet\ImageResize;

final class NewsService
{
    private NewsRepository $repository;
    // private OurGamesCreateValidator $createValidator;
    // private OurGamesUpdateValidator $updateValidator ,OurGamesCreateValidator $createValidator, OurGamesUpdateValidator $updateValidator;
    private ImageService $imageService;
    private LoggerInterface $logger;

    public function __construct(NewsRepository $repository, ImageService $imageService, LoggerFactory $loggerFactory)
    {
        $this->repository = $repository;
        // $this->createValidator = $createValidator;
        // $this->updateValidator = $updateValidator;
        $this->imageService = $imageService;
        $this->logger = $loggerFactory->addFileHandler('Rummy_Website/News/News.log')->createLogger();
    }

    //Get All Data
    public function getAllNews(): NewsData
    {
        
        $news = $this->repository->getAllNews();        
        $result = new NewsData();
        
        foreach ($news as $newsRow) {

            if($newsRow['image'] == null){
                $image = '';
            }else{
                $img_file = '../uploads/website/news_image/'.$newsRow['image'];
                $image = $this->imageService->imageString($img_file);

            }     

            $news = new NewsDataRead();
            $news->id = $newsRow['id'];
            $news->title = $newsRow['title'];
            $news->image = $image;
            $news->sub_description = $newsRow['sub_description'];
            $news->content = $newsRow['content'];
            $news->link = $newsRow['link'];
            $news->status = $newsRow['status'];
            $news->created_at = $newsRow['created_at'];
        

            $result->news[] = $news;
        }

        return $result;
        
    }

    //Get Data
    public function getNews(): NewsData
    {
        $news = $this->repository->getNews();
        $result = new NewsData();

        foreach ($news as $newsRow) {

            if($newsRow['image'] == null){
                $image = '';
            }else{
                $img_file = '../uploads/website/news_image/'.$newsRow['image'];
                $image = $this->imageService->imageString($img_file);

            }     

            $news = new NewsDataRead();
            $news->id = $newsRow['id'];
            $news->title = $newsRow['title'];
            $news->image = $image;
            $news->sub_description = $newsRow['sub_description'];
            $news->content = $newsRow['content'];
            $news->link = $newsRow['link'];
            $news->status = $newsRow['status'];
            $news->created_at = $newsRow['created_at'];
        

            $result->news[] = $news;
        }

        return $result;
       
    }

    //Get Latest Data
    public function getLatestNews(): NewsData
    {
        $news = $this->repository->getLatestNews();
        $result = new NewsData();

        foreach ($news as $newsRow) {

            if($newsRow['image'] == null){
                $image = '';
            }else{
                $img_file = '../uploads/website/news_image/'.$newsRow['image'];
                $image = $this->imageService->imageString($img_file);

            }    

            $news = new NewsDataRead();
            $news->id = $newsRow['id'];
            $news->title = $newsRow['title'];
            $news->image = $image;
            $news->sub_description = $newsRow['sub_description'];
            $news->content = $newsRow['content'];
            $news->link = $newsRow['link'];
            $news->status = $newsRow['status'];
            $news->created_at = $newsRow['created_at'];
        

            $result->news[] = $news;
        }

        return $result;
       
    }

    //Get One Data
    public function getOneNews(int $newsId): NewsData
    {
        $news = $this->repository->getOneNews($newsId);
        $result = new NewsData();

        foreach ($news as $newsRow) {

            if($newsRow['image'] == null){
                $image = '';
            }else{
                $img_file = '../uploads/website/news_image/'.$newsRow['image'];
                $image = $this->imageService->imageString($img_file);

            }    


            $news = new NewsDataRead();
            $news->id = $newsRow['id'];
            $news->title = $newsRow['title'];
            $news->image = $image;
            $news->sub_description = $newsRow['sub_description'];
            $news->content = $newsRow['content'];
            $news->link = $newsRow['link'];
            $news->status = $newsRow['status'];         
            $news->created_at = $newsRow['created_at'];

            $result->news[] = $news;
        }

        return $result;
    }

    //Insert
    public function insertNews(array $data, $uploadedFile): int
    {

        //var_dump($uploadedFile); exit;

        //File Uploads
        try{
            $directory =  "../uploads/website/news_image/";
            $small_directory="../uploads/website/news_image/small/";
            $medium_directory="../uploads/website/news_image/medium/";
            $large_directory="../uploads/website/news_image/large/";
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
                        $newsId = $this->repository->insertNews($data, $filename); 
                        $this->logger->info(sprintf('News Image  request id: %s', $newsId));

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
    public function updateNews(array $data, int $news_id, $uploadedFile): int
    {

        //var_dump($uploadedFile); exit;

        $uploadFileName = $uploadedFile->getClientFilename();

       //File Uploads
        try{
            if(!empty($uploadFileName)){
                $directory =  "../uploads/website/news_image/";
                $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
                $size = $uploadedFile->getSize();
                $basename = bin2hex(random_bytes(8));
                $filename = sprintf('%s.%0.8s', $basename, $extension); 
                //var_dump($extension); exit;
                if($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg'){
                
                    if($size <= 2097152){
                        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                           
                            $uploadedFile->moveTo($directory .$filename);
                            
                            $newsId = $this->repository->updateNews($data, $news_id,  $filename); 
                            $this->logger->info(sprintf('News Image  request id: %s', $newsId));
     
                            return $newsId;
                        }else{
                            return 11;
                        }
                    }else{
                        return 22;
                    } 
                }else{
                    return 33;
                }
            }else{
                $newsId = $this->repository->updateNews($data, $news_id, ''); 
                return $newsId;
            }              
        }catch(Exception $ex){
            var_dump($ex);
        }    
    }

    //status change
    public function changeStatus(int $news_id, int $status): int
    {
        

        $news = $this->repository->changeStatus($news_id, $status);

        $this->logger->info(sprintf('Blocked or Unblocked Successfully: %s', $news_id));

        return $news;
    }
  

    //Delete Data
    public function deleteNews(int $newsId): int
    {
        $news = $this->repository->deleteNews($newsId);

        $this->logger->info(sprintf('News delete successfully: %s', $newsId));

        return $news;
    }

   

}
