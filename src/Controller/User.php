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
        $rs = $sql->fetch();
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


    public function getUserInfo(Request $request, Response $response): Response
    {

        $data = $request->getQueryParams();
        $user_idx = $data["user_idx"];

        $db = $this->container->get('db');

        $sql = $db->prepare("select * from users where idx=".$user_idx);
        
        $rs = $sql->execute();
        $rs = $sql->fetchAll();

        if(count($rs) > 0){

            $sql2 = $db->prepare("select count(*) as count from posts where user_idx=".$user_idx);
            $rs2 = $sql2->execute();
            $rs2 = $sql2->fetchAll();
            $post_count = $rs2[0]['count'];


            $sql2 = $db->prepare("select count(*) as count from follow where base_idx=".$user_idx);
            $rs2 = $sql2->execute();
            $rs2 = $sql2->fetchAll();
            $follower = $rs2[0]['count'];

            $sql2 = $db->prepare("select count(*) as count from follow where from_idx=".$user_idx);
            $rs2 = $sql2->execute();
            $rs2 = $sql2->fetchAll();
            $following = $rs2[0]['count'];

            $sendData = [
                'message' => 'OK',
                // 'result' => 'OK',
                'result' => count($rs),
                'name' => $rs[0]['name'],
                'email' => $rs[0]['email'],
                'post_count' => $post_count,
                'follower' => $follower,
                'following' => $following
            ];
            return $response->withJson($sendData);
        }
    }


    public function getFollowChk(Request $request, Response $response): Response
    {

        $data = $request->getQueryParams();
        $user_idx = $data["user_idx"];
        $target_idx = $data['target_idx'];

        $db = $this->container->get('db');

        $sql = $db->prepare("
        select count(*) as count
        from follow
        where base_idx=".$target_idx."
        and from_idx=".$user_idx."
        ");

        $rs = $sql->execute();
        $rs = $sql->fetch();
        
        $sendData = [
            'message' => 'OK',
            'result' => 'OK',
            // 'result' => count($rs),
            'isFollow' => $rs['count'],
        ];
        return $response->withJson($sendData);

    }


    public function setFollow(Request $request, Response $response): Response
    {

        $data = $request->getParsedBody();
        $user_idx = $data["user_idx"];
        $target_idx = $data['target_idx'];

        $db = $this->container->get('db');

        $sql = $db->prepare("
        select count(*) as count
        from follow
        where base_idx=".$target_idx."
        and from_idx=".$user_idx."
        ");

        $rs = $sql->execute();
        $rs = $sql->fetch();
        
        if($rs['count'] == 0){
            
            $sql2 = $db->prepare("
            insert follow SET
            base_idx=".$target_idx.",
            from_idx=".$user_idx."
            ");

            $rs2 = $sql2->execute();

            $sendData = [
                'message' => 'OK',
                'result' => 'insert',
                // 'result' => count($rs),
            ];
            return $response->withJson($sendData);

        }else{

            $sql2 = $db->prepare("
            delete from follow where
            base_idx=".$target_idx." and
            from_idx=".$user_idx."
            ");

            $rs2 = $sql2->execute();

            $sendData = [
                'message' => 'OK',
                'result' => 'delete',
                // 'result' => count($rs),
            ];
            return $response->withJson($sendData);
        }

    }



}
