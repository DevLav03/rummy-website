<?php

namespace App\Action\Rummy_Website\News;

//Service
use App\Domain\Rummy_Website\News\Service\NewsService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class NewsAction
{
    private NewsService $service;
    private JsonRenderer $renderer;

    public function __construct(NewsService $service, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }

    //Get All Data
    public function getAllNews(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
       
        $news = $this->service->getAllNews();

        $ret=array("response"=>"success", "data"=>$news);
        
        return $this->renderer->json($response, $ret);

    }

    //Get Data
    public function getNews(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
       
        $news = $this->service->getNews();

        $ret=array("response"=>"success", "data"=>$news);
        
        return $this->renderer->json($response, $ret);

    }


    //Get Latest Data
    public function getLatestNews(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
       
        $news = $this->service->getLatestNews();

        $ret=array("response"=>"success", "data"=>$news);
        
        return $this->renderer->json($response, $ret);

    }

    //Get One Data
    public function getOneNews(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface { 

        $newsId = (int)$args['news-id'];
        
        $news = $this->service->getOneNews($newsId); 

        if(!empty($news)){

            $ret=array("response"=>"success", "data"=>$news);

        }else{

            $ret=array("response"=>"failure", "err_message"=>'No data found');

        }

        return $this->renderer->json($response, $ret);
    }
    

    //Insert Data
    public function insertNews(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

        $data = (array)$request->getParsedBody();
        $uploadedFiles = $request->getUploadedFiles();
        unset($data['payload']); 

        
        
        if (array_key_exists('image', $uploadedFiles)) {
            $file = $uploadedFiles['image']; 
            $imageInsert = $this->service->insertNews($data, $file);
        }
        else{
            $imageInsert = $this->service->insertNews($data, null);
        } 
       
        if($imageInsert == 0){
            $ret=array("response"=>"success", "message"=>'Insert Successfully');
        }else if($imageInsert == 1){
            $ret=array("response"=>"failure", "err_message"=>'Upload File Error');
        }else if($imageInsert == 2){
            $ret=array("response"=>"failure", "err_message"=>'Upload File Size Limit 2mb');
        }else if($imageInsert == 3){
            $ret=array("response"=>"failure", "err_message"=>'Updalod File Type png,jpg,jpeg');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No Data Insert');
        }
    
        return $this->renderer->json($response, $ret);
    }
    
    //Update Data
    public function updateNews(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {

        $data = (array)$request->getParsedBody();
        $news_id = (int)$args['news-id'];
        $uploadedFiles = $request->getUploadedFiles();
        unset($data['payload']); 
        
        
        if (array_key_exists('images', $uploadedFiles)) {
            $file = $uploadedFiles['images']; 
            $imageInsert = $this->service->updateNews($data, $news_id, $file);
        }
        else{
            $imageInsert = $this->service->updateNews($data, $news_id, null);
        } 
    
        if($imageInsert == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($imageInsert == 11){
            $ret=array("response"=>"failure", "err_message"=>'Upload File Error');
        }else if($imageInsert == 22){
            $ret=array("response"=>"failure", "err_message"=>'Upload File Size Limit 2mb');
        }else if($imageInsert == 33){
            $ret=array("response"=>"failure", "err_message"=>'Updalod File Type png,jpg,jpeg'); 
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No Data Update');
        }
    
        return $this->renderer->json($response, $ret);
    }

    //status change
    public function changeStatus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $news_id = (int)$args['news-id'];
        $status = (int)$args['status'];
       
        $news = $this->service->changeStatus($news_id, $status);

        if($news == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($news == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Updated');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }
        
        return $this->renderer->json($response, $ret);

    }

    //Delete Data
    public function deleteNews(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $news_Id = (int)$args['news-id'];
        $news = $this->service->deleteNews($news_Id);

        if($news == 1){
            $ret=array("response"=>"success", "message"=>'Delete Successfully');
        }else if($news == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Delete');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }

        return $this->renderer->json($response, $ret);

    }

}
