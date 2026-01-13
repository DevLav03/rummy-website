<?php

namespace App\Action\Admin_Panel\Auth;

use App\Service\Auth\AuthService;

use App\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

use Nyholm\Psr7\Factory\Psr17Factory;

use App\Exception\AuthException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use DomainException;
use InvalidArgumentException;
use UnexpectedValueException;

final class AuthAction
{
    private AuthService $authService;
    private JsonRenderer $renderer;
   
    public function __construct(AuthService $authService, JsonRenderer $jsonRenderer)
    {
        $this->authService = $authService;
        $this->renderer = $jsonRenderer;
    }

    public function generateTokens(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface 
    {
        $data = (array)$request->getParsedBody();
        $rToken = $data['refresh_token'];

        //parse token 
       
            $secretKey='abc123';
           
            try {   
                $decoded = JWT::decode($rToken, new Key($secretKey, 'HS256'));
                $payload = json_decode(json_encode($decoded), true);
                $object = $request->getParsedBody();
                $tokenList = $this->authService->createNewToken($payload);
                $ret=array("response"=>"success", "data"=>$tokenList);    
                return $this->renderer->json($response, $ret);
            } catch (InvalidArgumentException $e) {
                $responseFactory = new \Nyholm\Psr7\Factory\Psr17Factory();
                $response = $responseFactory->createResponse(401);
                return $response->withStatus(401);
            } catch (DomainException $e) {
                $responseFactory = new \Nyholm\Psr7\Factory\Psr17Factory();
                $response = $responseFactory->createResponse(401);
                return $response->withStatus(401);
            } catch (SignatureInvalidException $e) {
                $responseFactory = new \Nyholm\Psr7\Factory\Psr17Factory();
                $response = $responseFactory->createResponse(401);
                return $response->withStatus(401);
            } catch (BeforeValidException $e) {
                $responseFactory = new \Nyholm\Psr7\Factory\Psr17Factory();
                $response = $responseFactory->createResponse(401);
                return $response->withStatus(401);
            } catch (ExpiredException $e) {
                $responseFactory = new \Nyholm\Psr7\Factory\Psr17Factory();
                $response = $responseFactory->createResponse(401);
                return $response->withStatus(401);
            } catch (UnexpectedValueException $e) {
                $responseFactory = new \Nyholm\Psr7\Factory\Psr17Factory();
                $response = $responseFactory->createResponse(401);
                return $response->withStatus(401);
            }
           
    }
}      
