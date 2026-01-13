<?php

namespace App\Domain\Admin_Panel\HaodaPayment\Service;

use GuzzleHttp\Client;
use Exception;
use JsonException;


/**
 * Service
 */
final class PaymentGatewayService
{ 
    private string $client_secret;
    public string $client_id;
    public function __construct()
    {
        $this->client_id='nSPQx0f4YV1340';
        $this->client_secret='Yb7FNDcws2230324012054';
    }
    function getPublicIP() {
        // create & initialize a curl session
        $curl = curl_init();
      
        // set our url with curl_setopt()
        curl_setopt($curl, CURLOPT_URL, "http://httpbin.org/ip");
      
        // return the transfer as a string, also with setopt()
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      
        // curl_exec() executes the started curl session
        // $output contains the output string
        $output = curl_exec($curl);
      
        // close curl resource to free up system resources
        // (deletes the variable made by curl_init)
        curl_close($curl);
      
        $ip = json_decode($output, true);
      
        return $ip['origin'];
      }
    public function payoutRequest($order_info){
        try{
            // $body_json=json_encode([
            //     'account_number' => $order_info['beneficiary_account_number'],
            //     'account_ifsc' => $order_info['beneficiary_account_ifsc'],
            //     'bankname' => $order_info['beneficiary_account_name'],
            //     'confirm_acc_number' => $order_info['beneficiary_account_number'],
            //     'requesttype' => 'IMPS',
            //     'beneficiary_name' => $order_info['beneficiary_account_name'],
            //     'amount' => $order_info['amount'],
            //     'narration' => 'Withdraw_Request_From_User_'. $order_info['user_id'],
            //     'reference' => $order_info['order_id']
            // ]);
            //print_r($body_json);exit;
            // $curl = curl_init();

            // curl_setopt_array($curl, array(
            //     CURLOPT_URL => 'https://api.haodapayments.com/api/v3/bank/payout',
            //     CURLOPT_RETURNTRANSFER => true,
            //     CURLOPT_ENCODING => '',
            //     CURLOPT_MAXREDIRS => 10,
            //     CURLOPT_TIMEOUT => 10,
            //     CURLOPT_FOLLOWLOCATION => true,
            //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            //     CURLOPT_CUSTOMREQUEST => 'POST',
            //     CURLOPT_POSTFIELDS => $body_json,
            //     CURLOPT_HTTPHEADER => array(
            //         'x-client-id: nSPQx0f4YV1340',
            //         'x-client-secret: Yb7FNDcws2230324012054'
            //     )
            // ));

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://kepler.haodapayments.com/api/v1/payout/initiate',
                CURLOPT_CUSTOMREQUEST => 'POST'
              ));
            $response = curl_exec($curl);
            curl_close($curl);
            echo ($response);  exit;
            

            // CURLOPT_POSTFIELDS =>'{
            //     "account_number": "'.$order_info['beneficiary_account_number'].'",
            //     "account_ifsc": "'.$order_info['beneficiary_account_ifsc'].'",
            //     "bankname": "'.$order_info['beneficiary_account_name'].'",
            //     "confirm_acc_number" : "'.$order_info['beneficiary_account_number'].'",
            //     "requesttype" : "IMPS",
            //     "beneficiary_name" : "'.$order_info['beneficiary_account_name'].'",
            //     "amount" : "'.$order_info['amount'].'",
            //     "narration" : "Withdraw_Request_From_User_'. $order_info['user_id'].'",
            //     "reference" : "'.$order_info['order_id'].'"
            //   }', CURLOPT_HTTPHEADER => array(
            //     'x-client-id: nSPQx0f4YV1340',
            //     'x-client-secret: Yb7FNDcws2230324012054'
            //   ),
           // echo "ip is :". $this->getPublicIP();exit;

            // $curl = curl_init();
            // curl_setopt_array($curl, array(
            //     CURLOPT_URL => 'https://kepler.haodapayments.com/api/v1/payout/initiate',
            //     CURLOPT_RETURNTRANSFER => true,
            //     CURLOPT_ENCODING => '',
            //     CURLOPT_MAXREDIRS => 10,
            //     CURLOPT_TIMEOUT => 10,
            //     CURLOPT_FOLLOWLOCATION => true,
            //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            //     CURLOPT_CUSTOMREQUEST => 'POST',
            //     CURLOPT_POSTFIELDS =>'{
            //       "account_number": "50100172520574",
            //       "account_ifsc": "HDFC0000082",
            //       "bankname": "HDFC Pvt Ltd",
            //       "confirm_acc_number" : "50100172520574",
            //       "requesttype" : "IMPS",
            //       "beneficiary_name" : "Madhavan D",
            //       "amount" : "100",
            //       "narration" : "Withdraw_Request_From_User_Asha",
            //       "reference" : "'.$order_info['order_id'].'"
            //     }',
            //     CURLOPT_HTTPHEADER => array(
            //       'x-client-id: nSPQx0f4YV1340',
            //       'x-client-secret: Yb7FNDcws2230324012054'
            //     ),
            //   ));
            // $response = curl_exec($curl);
            // curl_close($curl);
            // echo ($response);  exit;
            
            
            // $info = curl_getinfo($curl);
            // var_dump($info); exit;
            //$response = '{"status_code": "200", "status": "Processing", "message": "Kindly allow some time for the payout to process","payout_id": "HOAD974138602500"}';    

            if($this->isJson($response)){
                $payout_res=json_decode($response, true);
            }else{
                //error log should be added here and return value should be false
                var_dump($response);exit;
                return $response;
            }

            //Return Respose
            if($payout_res['status_code'] == 200){
                return array('status'=>'success','message'=>$payout_res, 'str_res'=>$response);
            }else{
                return array('status'=>'failure','message'=>$payout_res, 'str_res'=>$response);
            }

        }catch(Exception $ex){
              //error log should be added here and return value should be false
            var_dump($ex);exit;
        }
    }

    function isJson(string $value): bool
    {
        try {
            json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return false;
        }
        return true;
    }

    //Not used now
    // public function payoutRequestGuzzle($order_info)
    // {
    //     //print_r($order_info);exit;
    //    try{
    //         $client = new Client();
    //         $headers = [
    //             'x-client-id' => $this->client_id,
    //             'x-client-secret' => $this->client_secret
    //         ];
    //         $body_json=json_encode([
    //             'account_number' => $order_info['beneficiary_account_number'],
    //             'account_ifsc' => $order_info['beneficiary_account_ifsc'],
    //             'bankname' => $order_info['beneficiary_account_name'],
    //             'confirm_acc_number' => $order_info['beneficiary_account_number'],
    //             'requesttype' => 'IMPS',
    //             'beneficiary_name' => $order_info['beneficiary_account_name'],
    //             'amount' => $order_info['amount'],
    //             'narration' => 'Withdraw Request From User '. $order_info['user_id'],
    //             'reference' => $order_info['order_id']
    //         ]);
    //         $request = new \GuzzleHttp\Psr7\Request('POST', 'https://api.haodapayments.com/api/v3/bank/payout', ['verify' => false], $headers, $body_json);
    //         $res = $client->sendAsync($request)->wait();
    //         echo "Test";echo $res->getBody();exit;
    //     }catch(Exception $ex){
    //         echo "error";var_dump($ex);exit;
    //     }
    //     // $client = new \GuzzleHttp\Client();
    //     // $options=array();
    //     // $options= [ 
    //     //     'verify' => false,
    //     //     'headers' => [
    //     //         'Accept' => 'application/json', 
    //     //         'x-client-id' => $this->client_id,
    //     //         'x-client-secret' => $this->client_secret
    //     //     ],
    //     //     'json' => [
    //     //         'account_number' => $order_info['beneficiary_account_number'],
    //     //         'account_ifsc' => $order_info['beneficiary_account_ifsc'],
    //     //         'bankname' => $order_info['beneficiary_account_name'],
    //     //         'confirm_acc_number' => $order_info['beneficiary_account_number'],
    //     //         'requesttype' => 'IMPS',
    //     //         'beneficiary_name' => $order_info['beneficiary_account_name'],
    //     //         'amount' => $order_info['amount'],
    //     //         'narration' => 'Withdraw Request From User '. $order_info['user_id'],
    //     //         'reference' => $order_info['order_id']
    //     //     ]
    //     //     ];
    //     //     $response = $client->request('POST', 'https://api.haodapayments.com/api/v3/bank/payout', $options);
    //     //     echo $response->getStatusCode(); // 200
    //     //     echo $response->getHeaderLine('content-type'); // 'application/json; charset=utf8'
    //     //     echo $response->getBody(); // '{"id": 1420053, "name": "guzzle", ...}'
        
    // }

    public function checkPayoutStatus($data){

        //print_r($data); exit;

        try{

            $body_json=json_encode([
                'payout_id' => $data['payout_id']
            ]);

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.haodapayments.com/api/v3/bank/payoutstatus',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_POSTFIELDS => $body_json,
                CURLOPT_HTTPHEADER => array(
                    'x-client-id: nSPQx0f4YV1340',
                    'x-client-secret: Yb7FNDcws2230324012054'
                )
            ));

             $response = curl_exec($curl);
             curl_close($curl);

             //echo "Output : "; var_dump($response); exit;

           // $response = '{"status_code": "200", "data":{"status":"Credited", "UTR":"1234567"}}';         

            if($this->isJson($response)){
                $payout_res=json_decode($response, true);
            }else{
                var_dump($response);exit; 
           }
           
            return $payout_res;
          

        }catch(Exception $ex){
            echo "error";
            var_dump($ex);exit;
        }

    }
}