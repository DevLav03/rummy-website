<?php

namespace App\Domain\Rummy_Website\FAQ\Service;

//Data
use App\Domain\Rummy_Website\FAQ\Data\FaqData;
use App\Domain\Rummy_Website\FAQ\Data\FaqDataRead;

//Validator
use App\Domain\Rummy_Website\FAQ\Validator\FaqCreateValidator;
use App\Domain\Rummy_Website\FAQ\Validator\FaqUpdateValidator;

//Repository
use App\Domain\Rummy_Website\FAQ\Repository\FaqRepository;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;


final class FaqService
{
    private FaqRepository $repository;
    private FaqCreateValidator $createValidator;
    private FaqUpdateValidator $updateValidator ;

    private LoggerInterface $logger;

    public function __construct(FaqRepository $repository,FaqCreateValidator $createValidator, FaqUpdateValidator $updateValidator, LoggerFactory $loggerFactory)
    {
        $this->repository = $repository;
        $this->createValidator = $createValidator;
        $this->updateValidator = $updateValidator;

        $this->logger = $loggerFactory->addFileHandler('Rummy_Website/FAQ/Faq.log')->createLogger();
    }

    //Get All Data
    public function getAllFaq(): FaqData
    {
        $faq = $this->repository->getAllFaq();

        $result = new FaqData();

        foreach ($faq as $faqRow) {
            $faq = new FaqDataRead();
            $faq->id = $faqRow['id'];
            $faq->title = $faqRow['title'];
            $faq->answer = $faqRow['answer'];
            $faq->status = $faqRow['status'];
            $faq->created_at = $faqRow['created_at'];
        

            $result->faq[] = $faq;
        }

        return $result;
       
    }

    //Get Data
    public function getFaq(): FaqData
    {
        $faq = $this->repository->getFaq();

        $result = new FaqData();

        foreach ($faq as $faqRow) {
            $faq = new FaqDataRead();
            $faq->id = $faqRow['id'];
            $faq->title = $faqRow['title'];
            $faq->answer = $faqRow['answer'];
            $faq->status = $faqRow['status'];
            $faq->created_at = $faqRow['created_at'];
        

            $result->faq[] = $faq;
        }

        return $result;
    
    }

    //Get Latest Data
    public function getLatestFaq(): FaqData
    {
        $faq = $this->repository->getLatestFaq();

        $result = new FaqData();

        foreach ($faq as $faqRow) {
            $faq = new FaqDataRead();
            $faq->id = $faqRow['id'];
            $faq->title = $faqRow['title'];
            $faq->answer = $faqRow['answer'];
            $faq->status = $faqRow['status'];
            $faq->created_at = $faqRow['created_at'];
        

            $result->faq[] = $faq;
        }

        return $result;
       
    }
   //Insert Data
   public function insertFaq(array $data): int
    {
       
       $this->createValidator->validateCreateData($data);

       $faqId = $this->repository->insertFaq($data);

       $this->logger->info(sprintf('Faq created successfully: %s', $faqId));

       return $faqId;
    }
  
    //Update Data
    public function updateFaq(int $faqId, array $data): int
    { 
        $this->updateValidator->validateUpdateData($data);

        $faq = $this->repository->updateFaq($faqId, $data);

        $this->logger->info(sprintf('Faq updated successfully: %s', $faqId));

        return $faq;
    }

    //Status change
    public function changeStatus(int $faq_id, int $status): int
    {
        // print_r($admin_id); exit;

        $faq = $this->repository->changeStatus($faq_id, $status);

        $this->logger->info(sprintf('Blocked or Unblocked Successfully: %s', $faq_id));

        return $faq;
    }
  

    //Delete Data
    public function deleteFaq(int $faqId): int
    {
        $faq = $this->repository->deleteFaq($faqId);

        $this->logger->info(sprintf('Faq delete successfully: %s', $faqId));

        return $faq;
    }

   

}
