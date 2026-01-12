<?php

namespace App\Domain\Rummy_Game\Kyc_Verify\Service;

//Data
use App\Domain\Rummy_Game\Kyc_Verify\Data\KycVerifyData;
use App\Domain\Rummy_Game\Kyc_Verify\Data\KycVerifyDataRead;
use App\Domain\Rummy_Game\Kyc_Verify\Data\KycVerifyDatas;
use App\Domain\Rummy_Game\Kyc_Verify\Data\KycVerifyDatasRead;

//Validator
use App\Domain\Rummy_Game\Kyc_Verify\Validator\KycVerifyValidator;
use App\Domain\Admin_Panel\Withdraw_Request\Validator\WithdrawReqValidator;

//Service
use App\Service\Mail\MailService;
use App\Domain\Master_Table\Default_Mail\Service\DefaultMailService;

//Image Service
use App\Service\Image\ImageService;
use \Gumlet\ImageResize;

//Repository
use App\Domain\Rummy_Game\Kyc_Verify\Repository\KycVerifyRepository;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;

final class KycVerifyService
{
    private KycVerifyRepository $repository;
    private KycVerifyValidator $kycVerifyValidator;
    private WithdrawReqValidator $withdrawReqValidator;

    private LoggerInterface $logger;
    private MailService $mailService;
    private DefaultMailService $service;
    private ImageService $imageService;


    public function __construct(KycVerifyRepository $repository, DefaultMailService $service, ImageService $imageService, LoggerFactory $loggerFactory, MailService $mailService,  KycVerifyValidator $kycVerifyValidator, WithdrawReqValidator $withdrawReqValidator)
    {
        $this->repository = $repository;
        $this-> KycVerifyValidator = $kycVerifyValidator;
        $this->withdrawReqValidator = $withdrawReqValidator;
        $this->service = $service;
        $this->mailService = $mailService;
        $this->imageService = $imageService;

        $this->logger = $loggerFactory->addFileHandler('Rummy_Game/Kyc_Verify/KycVerify.log')->createLogger();
    }

    //Get All Data
    public function getKycverifys(array $data): KycVerifyData
    {
        $this->withdrawReqValidator->validateData($data);

        $kycverifys = $this->repository->getKycverifys($data);
        // print_r($kycverifys); exit;
        // $img_file = '../uploads/kyc-verify'.$kycverifys[0]['pc_file'];
        
        // $imgData = base64_encode(file_get_contents($img_file));

        
        // $src = 'data: '.mime_content_type($img_file).';base64,'.$imgData;


        $result = new KycVerifyData();

        foreach ($kycverifys as $kycverifyRow) {

            if($kycverifyRow['pc_file'] == null){
                $image = '';
            }else{
                $img_file = '../uploads/kyc-verify/large/'.$kycverifyRow['pc_file']; 
                $image = $this->imageService->imageString($img_file);

            }

            $kycverify = new KycVerifyDataRead();
            $kycverify->id = $kycverifyRow['id'];
            $kycverify->user_id = $kycverifyRow['user_id'];
            $kycverify->name = $kycverifyRow['name'];
            $kycverify->username = $kycverifyRow['username'];
            $kycverify->phone_no = $kycverifyRow['phone_no'];
            $kycverify->email = $kycverifyRow['email'];
            $kycverify->kyc_verify_status = $kycverifyRow['kyc_verify_status'];
            $kycverify->phone_verify_status = $kycverifyRow['phone_verify_status'];
            $kycverify->pan_no = $kycverifyRow['pan_no'];
            $kycverify->pc_file = $image;
            $kycverify->pc_verify_status = $kycverifyRow['pc_verify_status'];
            $kycverify->pc_requested_on = $kycverifyRow['pc_requested_on'];
            $kycverify->pc_verified_by = $kycverifyRow['pc_verified_by'];
            $kycverify->pc_verify_by_ip_address = $kycverifyRow['pc_verify_by_ip_address'];
            $kycverify->pc_verified_on = $kycverifyRow['pc_verified_on'];


            $result->kyc_verify[] = $kycverify;
        }

        return $result; 
    
    }

    //Get One Data
    public function getUserKycVerify(array $data, int $userId): KycVerifyDatas
    {
        // print_r($userId); exit;
        $kycverifys = $this->repository->getUserKycVerify($data, $userId);

        $result = new KycVerifyDatas();

        foreach ($kycverifys as $kycverifyRow) {

            if($kycverifyRow['pc_file'] == null){
                $image = '';
            }else{
                $img_file = '../uploads/kyc-verify/large/'.$kycverifyRow['pc_file']; 
                // print_r($img_file); exit;
                $image = $this->imageService->imageString($img_file);

            }

            $kycverify = new KycVerifyDatasRead();
            $kycverify->id = $kycverifyRow['id'];
            $kycverify->user_id = $kycverifyRow['user_id'];
            $kycverify->kyc_verify_status = $kycverifyRow['kyc_verify_status'];
            $kycverify->pan_no = $kycverifyRow['pan_no'];
            $kycverify->pc_file = $image;
            $kycverify->created_at = $kycverifyRow['created_at'];

            $result->kyc_verify[] = $kycverify;
        }

        return $result;
    }


    //KycVerifyCount
    public function getKycverifysCount(array $data): int
    {
        $total = $this->repository->getKycverifysCount($data);
        return  $total ; 
    }

    //upload file
    public function getKycVerify(int $kycverifyId): array
    {
        $kycverifyRows = $this->repository->getKycVerify($kycverifyId);

        return $kycverifyRows;

    }


    //Insert
    public function insertKycVerify(array $data, $uploadedFile): int
    {

        $this->KycVerifyValidator->validateData($data);

        //File Uploads
        try{
            $directory =  "../uploads/kyc-verify";
            $small_directory="../uploads/kyc-verify/small/";
            $medium_directory="../uploads/kyc-verify/medium/";
            $large_directory="../uploads/kyc-verify/large/";
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
                        $kycId = $this->repository->insertKycVerify($data, $filename); 
                        $this->logger->info(sprintf('Kyc request id: %s', $kycId));

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

   //block and unblock
   public function statusKycVerify(int $kycverify_id, int $verify_status, int $admin_id, string $ip): int
   {
        $kycverify = $this->repository->statusKycVerify($kycverify_id, $verify_status, $admin_id, $ip);

        $user = $this->repository->getUserDetail($kycverify_id); 

        $user_id = $user[0]['id'];
        $user_name = $user[0]['name'];
        $user_email = $user[0]['email'];
    
        $this->logger->info(sprintf('Status Change Successfully: %s', $kycverify_id));

        if($verify_status == 1){

            $mail_details = $this->service->getMailTemplate('user_kyc_success');
            
            $content = $mail_details['message'];        
           
            $this->repository->changeUsersStatus($user_id);

        }else{

            $mail_details = $this->service->getMailTemplate('user_kyc_reject');

            $content = $mail_details['message'];    
        }

        //Send Mail
        $mail = array($user_email);
        $sender_name = array($mail_details['name']); 
        $sub = $mail_details['subject'];
        
        $body_content= $this->named_printf($content, array('name'=>$user_name));

        $sample_file_attachment='';

        $this->mailService->sendMail($mail, $sender_name, $sub, $body_content, $sample_file_attachment);

        return $kycverify;
    }

    function named_printf ($format_string, $values) {
        extract($values);
        $result = $format_string;
        eval('$result = "'.$format_string.'";');
        return $result;
    }

}
