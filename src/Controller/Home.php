<?php

declare(strict_types=1);

namespace App\Controller;

use App\CustomResponse as Response;
use Pimple\Psr11\Container;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Home
{
    private const API_NAME = 'slim4-api-skeleton';

    private const API_VERSION = '0.39.0';

    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getHelp(Request $request, Response $response): Response
    {
        $message = [
            'api' => self::API_NAME,
            'version' => self::API_VERSION,
            'timestamp' => time(),
        ];

        return $response->withJson($message);
    }

    public function getStatus(Request $request, Response $response): Response
    {
        $db = $this->container->get('db');

        $status = [
            'status' => [
                'database' => 'OK',
            ],
            'api' => self::API_NAME,
            'version' => self::API_VERSION,
            'timestamp' => time(),
        ];

        return $response->withJson($status);
    }
    public function postLogin(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $email = $data->get('email');
        $passwd = $data->get('passwd');

        echo "email: ". $email;
        echo "passwd: ". $passwd;

        $db = $this->container->get('db');
        $sql = $db->prepare("select * from users where email='".$email."' and passwd='".$passwd."'");


        $res = [
            'status' => [
                'message' => 'OK',
                'result' => 'OK',
                'data' => $sql,
            ],
        ];

        return $response->withJson($res);
    }
    

    

    public function postStatus(Request $request, Response $response): Response
    {
        $this->container->get('db');
        $status = [
            'status' => [
                'database' => 'OK',
            ],
            'api' => self::API_NAME,
            'version' => self::API_VERSION,
            'timestamp' => time(),
        ];

        return $response->withJson($status);
    }
    
    public function postUsers(Request $request, Response $response): Response
    {
        //echo 'CUSTOMERS';
        $sql = "SELECT * FROM user";
    
        /*
        try{
            
            // GET DB Object
            $db = $this->container->get('db');
            print $db;
            // Connect
            $db = $db->connect();
    
            $stmt = $db->query($sql);
            $customers = $stmt->fetchAll();
            $db = null;        
            echo json_encode($customers);
    
        }catch(PDOException $e){
            echo '{"error" : {"text" : '.$e->getMessage().'}}';
            
        }
        */


        $parsedBody = $request->getQueryParams();
        $data = $request->getParsedBody();

        $status = [
            'status' => [
                'database' => 'OK',
            ],
            //'header' => $request->getHeaders(),
            'body' => $sql,
        ];

        return $response->withJson($status);
    }
}
