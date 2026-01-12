<?php

namespace App\Action\Admin_Panel\Login;

//Data
use App\Domain\Admin_Panel\Login\Data\LoginData;
use App\Domain\Admin_Panel\Login\Data\LoginDataRead;

//Service
use App\Domain\Admin_Panel\Login\Service\LoginService;
use App\Service\Password\PasswordService;
use App\Service\IP_Address\IPAddressService;
use App\Service\Auth\AuthService;

use App\Renderer\JsonRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class LoginAction
{
    private LoginService $service;
    private PasswordService $passwordService;
    private IPAddressService $ipAddressService;

    private JsonRenderer $renderer;
    private AuthService $authService;

    public function __construct(LoginService $service, AuthService $authService, JsonRenderer $jsonRenderer, PasswordService $passwordService, IPAddressService $ipAddressService)
    {
        $this->service = $service;
        $this->passwordService = $passwordService;
        $this->ipAddressService = $ipAddressService;
        $this->renderer = $jsonRenderer;
        $this->authService = $authService;
    }

    public function adminLogin(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {

        $data = (array)$request->getParsedBody();

        $username = $data['username'];
        $password = $this->passwordService->passwordEncrytion($data['password']);

        $ip_info = $this->ipAddressService->getIpAddress($request->getServerParams());

        $loginData = $this->service->adminLogin($username, $password, $ip_info);

        if(!empty($loginData) && count($loginData)>0){

            if($loginData[0]['res'] == 'success'){
                $result = new LoginData();
                foreach($loginData as $adminRow){
                    $admin = new LoginDataRead();
                    $token_list = $this->authService->createNewToken($adminRow);
                    $admin->id = $adminRow['id'];
                    $admin->name = $adminRow['name'];
                    $admin->username = $adminRow['username'];
                    $admin->role_id = $adminRow['role_id'];
                    $admin->role_name = $adminRow['role_name'];
                    $admin->role_type = $adminRow['role_type'];
                    $admin->scope_list = $adminRow['scope'];
                    $admin->token = $token_list['token'];
                    $admin->refresh_token = $token_list['refresh_token'];
                    $result->login_user[] = $admin;
                }
                $ret=array("response"=>"success", "data"=>(array)$result->login_user[0]);
            }else{
                $ret=array("response"=>"failure", "err_message"=>$loginData[0]['msg']);
            }

        }else{
            $ret=array("response"=>"failure", "err_message"=>"Invalid Login");
        }
        return $this->renderer->json($response, $ret);
    }

    //Get Current Admin Users
    public function getCurrentUser(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface { 

        $data = (array)$request->getParsedBody();
        $adminId = (int)$data['payload']['id'];
        unset($data['payload']); 
 
        $admins = $this->service->getCurrentUser($adminId); 

        if(!empty($admins)){

            $ret=array("response"=>"success", "data"=>$admins);

        }else{

            $ret=array("response"=>"failure", "err_message"=>'No data found');

        }

        return $this->renderer->json($response, $ret);
    }

    //Logout 
    public function adminLogout(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {

        $data = (array)$request->getParsedBody();
        $admin_id = $data['payload']['id'];
        unset($data['payload']); 

        $ip_info = $this->ipAddressService->getIpAddress($request->getServerParams());

        $data['admin_id'] = $admin_id;
        $data['ip_address'] = $ip_info['ip_address'];
        $data['device_type'] = $ip_info['device_type'];
        $data['action'] = 'Logout';
        
        $admin = $this->service->adminLogout($data);
       
        if(!empty($admin)){
            $ret=array("response"=>"success", "message"=>'Logout Successfully!');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'Logout Failed!');
        }

        return $this->renderer->json($response, $ret);

    }


    //Admin Log History
    public function logAdminHistory(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $admin_id = (int)$args['admin-id'];

        $data = (array)$request->getParsedBody();
        unset($data['payload']); 
        
        $admins =$this->service->logAdminHistory($data, $admin_id);
        $total =$this->service->logAdminHistoryCount($data, $admin_id);

        if(!empty($admins)){
            $ret=array("response"=>"success", "data"=>[$admins,['total'=>$total]]);
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No data found');
        }

        return $this->renderer->json($response, $ret);
      
    }

    //Admin Time-in
    public function timeinAdmin(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $admin_id = (int)$args['admin-id'];

        
        $admins =$this->service->timeinAdmin($admin_id);

        if(!empty($admins)){
            $ret=array("response"=>"success", "message"=>'Time in Update Successfully');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No data found');
        }

        return $this->renderer->json($response, $ret);
    }

    //Admin Time-out
    public function timeoutAdmin(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        //$admin_id = (int)$args['admin-id'];

        $data = (array)$request->getParsedBody();
        $admin_id = $data['payload']['id'];
        unset($data['payload']);


        $admins =$this->service->timeoutAdmin($admin_id);

        if(!empty($admins)){
            $ret=array("response"=>"success", "message"=>'Time out Update Successfully');
        }else{
            $ret=array("response"=>"failure", "err_message"=>'No data found');
        }

        return $this->renderer->json($response, $ret);
    }
}

