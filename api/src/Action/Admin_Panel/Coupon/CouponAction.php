<?php

namespace App\Action\Admin_Panel\Coupon;

//Service
use App\Domain\Admin_Panel\Coupon\Service\CouponService;


use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class CouponAction
{
    private CouponService $service;
    private JsonRenderer $renderer;

    public function __construct(CouponService $service, JsonRenderer $jsonRenderer)
    {
        $this->service = $service;
        $this->renderer = $jsonRenderer;
    }

    //Get All Data
    public function getCoupon(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
       
        $coupons = $this->service->getCoupon();

        $ret=array("response"=>"success", "data"=>$coupons);
        
        return $this->renderer->json($response, $ret);

    }

    //Get One Data
    public function getOneCoupon(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface { 

        $couponId = (int)$args['coupon-id'];
        
        $coupon = $this->service->getOneCoupon($couponId); 

        if(!empty($coupon)){

            $ret=array("response"=>"success", "data"=>$coupon);

        }else{

            $ret=array("response"=>"failure", "err_message"=>'No data found');

        }

        return $this->renderer->json($response, $ret);
    }


    //Insert Data
    public function insertCoupon(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

        $data = (array)$request->getParsedBody();
        unset($data['payload']);   

        $coupon = $this->service->insertCoupon($data);

        if(!empty($coupon)){
            $ret=array("response"=>"success", "data"=>["coupon-id"=>$coupon]);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No Data Insert');
        }
          
        
        return $this->renderer->json($response, $ret);
    }


    //Update Data
    public function updateCoupon(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
       
        $data=(array)$request->getParsedBody();
        unset($data['payload']);

        $coupon_id = (int)$args['coupon-id'];
        

        $coupon = $this->service->updateCoupon($coupon_id, $data);

        if($coupon == 1){
            $ret=array("response"=>"success", "message"=>'Update Successfully');
        }else if($coupon == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Updated');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }
         
    
        return $this->renderer->json($response, $ret);    
      
    }


    //Delete Data
    public function deleteCoupon(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $coupon_Id = (int)$args['coupon-id'];
        $coupon = $this->service->deleteCoupon($coupon_Id);

        if($coupon == 1){
            $ret=array("response"=>"success", "message"=>'Delete Successfully');
        }else if($coupon == 0){
            $ret=array("response"=>"failure", "err_message"=>'No Record Delete');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Something Went Wrong');
        }

        return $this->renderer->json($response, $ret);

    }
  

}
