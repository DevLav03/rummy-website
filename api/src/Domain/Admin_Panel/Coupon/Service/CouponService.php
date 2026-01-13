<?php

namespace App\Domain\Admin_Panel\Coupon\Service;

//Data
use App\Domain\Admin_Panel\Coupon\Data\CouponData;
use App\Domain\Admin_Panel\Coupon\Data\CouponDataRead;

//Validator
use App\Domain\Admin_Panel\Coupon\Validator\CouponCreateValidator;
use App\Domain\Admin_Panel\Coupon\Validator\CouponUpdateValidator;

//Repository
use App\Domain\Admin_Panel\Coupon\Repository\CouponRepository;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;


final class CouponService
{
    private CouponRepository $repository;
    private CouponCreateValidator $createValidator;
    private CouponUpdateValidator $updateValidator;

    private LoggerInterface $logger;

    public function __construct(CouponRepository $repository, CouponCreateValidator $createValidator,CouponUpdateValidator $updateValidator, LoggerFactory $loggerFactory) 
    {
        $this->repository = $repository;
        $this->createValidator = $createValidator;
        $this->updateValidator = $updateValidator;

        $this->logger = $loggerFactory->addFileHandler('Admin_Panel/Coupon/Coupons.log')->createLogger();
    }

    //Get All Data
    public function getCoupon(): CouponData
    {
        $coupon = $this->repository->getCoupon();

        $result = new CouponData();

        foreach ($coupon as $couponRow) {
            $coupon = new CouponDataRead();
            $coupon->id = $couponRow['id'];
            $coupon->coupon_title = $couponRow['coupon_title'];
            $coupon->coupon_code = $couponRow['coupon_code'];
            $coupon->valid_from_date = $couponRow['valid_from_date'];
            $coupon->valid_to_date = $couponRow['valid_to_date'];
            $coupon->bonus_type = $couponRow['bonus_type'];
            $coupon->bonus_value = $couponRow['bonus_value'];
            $coupon->max_price = $couponRow['max_price'];
            $coupon->reusable = $couponRow['reusable'];
            $coupon->created_at = $couponRow['created_at'];

            $result->coupon[] = $coupon;
        }

        return $result;
       
    }


    //Get One Data
    public function getOneCoupon(int $couponId): CouponData
    {
        $coupon = $this->repository->getOneCoupon($couponId);

        $result = new CouponData();

        foreach ($coupon as $couponRow) {
            $coupon = new CouponDataRead();
            $coupon->id = $couponRow['id'];
            $coupon->coupon_title = $couponRow['coupon_title'];
            $coupon->coupon_code = $couponRow['coupon_code'];
            $coupon->valid_from_date = $couponRow['valid_from_date'];
            $coupon->valid_to_date = $couponRow['valid_to_date'];
            $coupon->bonus_type = $couponRow['bonus_type'];
            $coupon->bonus_value = $couponRow['bonus_value'];
            $coupon->max_price = $couponRow['max_price'];
            $coupon->reusable = $couponRow['reusable'];
            $coupon->created_at = $couponRow['created_at'];

            $result->coupon[] = $coupon;
        }

        return $result;
    }


    //Insert Data
    public function insertCoupon(array $data): int
    {
        
        $this->createValidator->validateCreateData($data);

        $couponId = $this->repository->insertCoupon($data, $password);

        $this->logger->info(sprintf('Coupon created successfully: %s', $couponId));

        return $couponId;
    }


    //Update Data
    public function updateCoupon(int $couponId, array $data): int
    { 
        $this->updateValidator->validateUpdateData($data);

        $coupon = $this->repository->updateCoupon($couponId, $data);

        $this->logger->info(sprintf('Coupon updated successfully: %s', $couponId));

        return $coupon;
    }
   

    //Delete Data
    public function deleteCoupon(int $couponId): int
    {
        $Coupon = $this->repository->deleteCoupon($couponId);

        $this->logger->info(sprintf('Coupon delete successfully: %s', $couponId));

        return $Coupon;
    }
}
