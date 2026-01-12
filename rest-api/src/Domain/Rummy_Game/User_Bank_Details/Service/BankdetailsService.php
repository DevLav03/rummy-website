<?php

namespace App\Domain\Rummy_Game\User_Bank_Details\Service;

//Data
use App\Domain\Rummy_Game\User_Bank_Details\Data\BankdetailsData;
use App\Domain\Rummy_Game\User_Bank_Details\Data\BankdetailsDataRead;

//Validator
use App\Domain\Rummy_Game\User_Bank_Details\Validator\BankdetailsCreateValidator;
use App\Domain\Rummy_Game\User_Bank_Details\Validator\BankdetailsUpdateValidator;

//Repository
use App\Domain\Rummy_Game\User_Bank_Details\Repository\BankdetailsRepository;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;


final class BankdetailsService
{
    private BankdetailsRepository $repository;
    private BankdetailsCreateValidator $createValidator;
    private BankdetailsUpdateValidator $updateValidator;

    private LoggerInterface $logger;

    public function __construct(BankdetailsRepository $repository, LoggerFactory $loggerFactory, BankdetailsCreateValidator $createValidator, BankdetailsUpdateValidator $updateValidator) //
    {
        $this->repository = $repository;
        $this->createValidator = $createValidator;
        $this->updateValidator = $updateValidator;

        $this->logger = $loggerFactory->addFileHandler('Rummy_Game/User_Bank_Details/Bankdetails.log')->createLogger();
    }

    //Get All Data
    public function getBankDetails(): BankdetailsData
    {
        $bankdetails = $this->repository->getBankDetails();

        $result = new BankdetailsData();

        foreach ($bankdetails as $bankdetailsRow) {
            $bankdetails = new BankdetailsDataRead();
            $bankdetails->id = $bankdetailsRow['id'];
            $bankdetails->user_id = $bankdetailsRow['user_id'];
            $bankdetails->name = $bankdetailsRow['name'];
            $bankdetails->username = $bankdetailsRow['username'];
            $bankdetails->bank_name = $bankdetailsRow['bank_name'];
            $bankdetails->account_no = $bankdetailsRow['account_no'];
            $bankdetails->ifsc_code = $bankdetailsRow['ifsc_code'];
            $bankdetails->date = $bankdetailsRow['created_at'];

            $result->bankdetails[] = $bankdetails;
        }

        return $result;
       
    }

    //Get One Data
    public function getUserBankDetails(array $data, int $userId): array
    {
        // print_r($userId); exit;
        $bankdetails = $this->repository->getUserBankDetails($data, $userId);

        return $bankdetails;

        // $result = new KycVerifyData();

        // foreach ($kycverifys as $kycverifyRow) {

        //     if($kycverifyRow['pc_file'] == null){
        //         $image = '';
        //     }else{
        //         $img_file = '../uploads/kyc-verify/'.$kycverifyRow[0]['pc_file']; 
        //         $image = $this->imageService->imageString($img_file);

        //     }

        //     $kycverify = new KycVerifyDataRead();
        //     $kycverify->id = $kycverifyRow['id'];
        //     $kycverify->user_id = $kycverifyRow['user_id'];
        //     $kycverify->kyc_verify_status = $kycverifyRow['kyc_verify_status'];
        //     $kycverify->pan_no = $kycverifyRow['pan_no'];
        //     $kycverify->pc_file = $image;
        //     $kycverify->created_at = $created_at;

        //     $result->kyc_verify[] = $kycverify;
        // }

        // return $result;
    }


    //Bankdetails Count
    public function getBankDetailsCount(array $data): int
    {
        $total = $this->repository->getBankDetailsCount($data);
        return  $total ; 
    }

    //Insert Data
    public function insertBankDetails(array $data): int
    {
        
        $this->createValidator->validateCreateData($data);

        $bankdetailsId = $this->repository->insertBankDetails($data);

        $this->logger->info(sprintf('Bankdetails created successfully: %s', $bankdetailsId));

        return $bankdetailsId;
    }

    //Update Data
    public function updateBankDetails(int $bankdetailsid, array $data): int
    { 
        $this->updateValidator->validateUpdateData($data);

        $bankdetails = $this->repository->updateBankDetails($bankdetailsid, $data);

        $this->logger->info(sprintf('Bankdetails updated successfully: %s', $bankdetailsid));

        return $bankdetails;
    }

}
