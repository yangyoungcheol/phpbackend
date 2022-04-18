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


        $res = [
            'message' => 'OK',
            'result' => 'OK',
            'user_idx' => $user_idx,
            'count' => $count,
        ];

        return $response->withJson($res);
    }
    
    public function postUsers(Request $request, Response $response): Response
    {
        //echo 'CUSTOMERS';
        $data = $request->getParsedBody();
        $email = $data->get('email');
        $passwd = $data->get('passwd');

        $rs = [
            'result' => [
                'email' => $email,
                'passwd' => $passwd,
            ],
        ];

        return $response->withJson($rs);
    }
}
