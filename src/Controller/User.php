<?php

declare(strict_types=1);

namespace App\Controller;

use App\CustomResponse as Response;
use Pimple\Psr11\Container;
use Psr\Http\Message\ServerRequestInterface as Request;

final class User
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

    public function userLogin(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $email = $data["email"];
        $passwd = $data["passwd"];
        // $email = $data->post('email');
        // $passwd = $data->post('passwd');


        $db = $this->container->get('db');

        $sql = $db->prepare("select * from users where email='".$email."' and passwd='".$passwd."'");
        $rs = $sql->execute();
        $rs = $sql->fetchAll();

        $count = count($rs);
        $user_idx = $rs[0]["idx"];

        if($user_idx > 0){
            $res = [
                'message' => 'OK',
                'result' => 'OK',
                'user_idx' => $user_idx,
                'count' => $count,
            ];
        }else{
            $res = [
                'message' => 'Fail',
                'result' => 'Fail',
                'user_idx' => $user_idx,
                'count' => $count,
            ];

        }

        return $response->withJson($res);
    }
    
    public function postUsers(Request $request, Response $response): Response
    {
        //echo 'CUSTOMERS';
        $data = $request->getParsedBody();
        $email = $data["email"];
        $name = $data["name"];
        $passwd = $data["passwd"];

        
        $db = $this->container->get('db');

        $sql = $db->prepare("select count(*) as count from users where email='".$email."'");
        
        $rs = $sql->execute();
        //$rs = $sql->fetch();
        $count = $rs['count'];

        if($count > 0){

            $rs = [
                'message' => 'Duplicate email',
                'result' => 'Fail',
            ];
            return $response->withJson($rs);

        }else{


            $sql = $db->prepare("insert users set
            name='".$name."',
            email='".$email."',
            passwd='".$passwd."',
            c_date=now()
            ");

            $rs = $sql->execute();
            //$rs = $sql->fetchAll();

            if($rs){
                $rs = [
                    'message' => 'OK',
                    'result' => 'OK',
                ];
            }else{
                $rs = [
                    'message' => 'Fail',
                    'result' => 'Fail',
                ];

            }

            return $response->withJson($rs);
        }

    }
}
