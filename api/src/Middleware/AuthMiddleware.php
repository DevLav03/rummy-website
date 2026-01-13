<?php

namespace App\Middleware;

use DI\Container;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Slim\Exception\NotFoundException;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

use Nyholm\Psr7\Factory\Psr17Factory;
use Selective\BasePath\BasePathDetector;
use Selective\BasePath\BasePathMiddleware;

use \Firebase\JWT\JWT;
use App\Exception\AuthException;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface; 

use DomainException;
use InvalidArgumentException;
use UnexpectedValueException;
use Slim\Exception\httpUnauthorizedException; 
use Slim\Exception\HttpForbiddenException;

use App\Service\Auth\AuthService;

use PDO;

class AuthMiddleware 
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
        //LoggerFactory $loggerFactory $this->logger = $loggerFactory->addFileHandler('Request_15_11_2022.log')->createLogger();
    }
    public function __invoke(Request $request, RequestHandler $handler): Response {
        $token = $request->getHeaderLine('Authorization');
        
        if (empty($token) == true) {
            
            $responseFactory = new \Nyholm\Psr7\Factory\Psr17Factory();
            $response = $responseFactory->createResponse(200);
            return $response->withStatus(401);
        }
       
        try {
            $secretKey='abc123';
            //decrypt the token here
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
            //$decoded = JWT::decode($payload, $keys);
            $payload = json_decode(json_encode($decoded), true);
            $object = $request->getParsedBody();
            $object['payload'] = $payload;
            $admin_id=$object['payload']['id'];

            $routeContext = RouteContext::fromRequest($request);     
            $route = $routeContext->getRoute();                                                                                                                              
            $scope_name = $route->getArgument('scope');
            //print_r($scope_name);
            
            if(empty($scope_name)){
                $responseFactory = new \Nyholm\Psr7\Factory\Psr17Factory();
                $request->withParsedBody($object); 
                $response = $responseFactory->createResponse(200);
                $response = $handler->handle($request->withParsedBody($object)); 
                $response->withHeader('Content-Type', 'application/json');
                return $response;
                //throw new HttpForbiddenException($request);
            }else{
                if($this->validateScope($admin_id,trim($scope_name))){
                   
                    $responseFactory = new \Nyholm\Psr7\Factory\Psr17Factory();
                    $request->withParsedBody($object);  //var_dump($request);exit;
                    $response = $responseFactory->createResponse(200);
                    $response = $handler->handle($request->withParsedBody($object)); 
                    $response->withHeader('Content-Type', 'application/json');
                    return $response;
                }else{
                    throw new HttpForbiddenException($request);
                }
            }
           
        } catch (InvalidArgumentException $e) {
            // $responseFactory = new \Nyholm\Psr7\Factory\Psr17Factory();
            // $response = $responseFactory->createResponse(401);
            // return $response->withStatus(401);
            throw new HttpUnauthorizedException($request);
        } catch (DomainException $e) {
            // $responseFactory = new \Nyholm\Psr7\Factory\Psr17Factory();
            // $response = $responseFactory->createResponse(401);
            // return $response->withStatus(401);
            throw new HttpUnauthorizedException($request);
        } catch (SignatureInvalidException $e) {
            // $responseFactory = new \Nyholm\Psr7\Factory\Psr17Factory();
            // $response = $responseFactory->createResponse(401);
            // return $response->withStatus(401);
            throw new HttpUnauthorizedException($request);
        } catch (BeforeValidException $e) {
            // $responseFactory = new \Nyholm\Psr7\Factory\Psr17Factory();
            // $response = $responseFactory->createResponse(401);
            // return $response->withStatus(401);
            throw new HttpUnauthorizedException($request);
        } catch (ExpiredException $e) {
            // $responseFactory = new \Nyholm\Psr7\Factory\Psr17Factory();
            // $response = $responseFactory->createResponse(401);
            // return $response->withStatus(401);
            throw new HttpUnauthorizedException($request);
        } catch (UnexpectedValueException $e) {
            // $responseFactory = new \Nyholm\Psr7\Factory\Psr17Factory();
            // $response = $responseFactory->createResponse(401);
            // return $response->withStatus(401);
            throw new HttpUnauthorizedException($request);
        }
       
    }
    public function getScope($admin_id){
        $statement = $this->conn->prepare("SELECT mr.scope_list as scopes FROM `admins` au inner join `master_admin_roles` mr on au.role_id = mr.role_id where au.id=".$admin_id);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows;

    }
    public function validateScope($admin_id, $scope){ 
        try{
            $resp=$this->getScope($admin_id); 
            if(count($resp)>0 && !empty($resp[0]['scopes'])){
                $scope_list=json_decode($resp[0]['scopes']);
                
    
                if(in_array($scope,$scope_list)){
                    //print_r("scope exist");exit;
                    return true;
                }else{
                    //print_r("scope not exist");exit;
                    return false; 
                }
            }else{
                return false; 
            }
        }catch(Exception $ex){
            return false;
        }
    }

   
}