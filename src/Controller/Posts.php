<?php
declare(strict_types=1);

namespace App\Controller;

use App\CustomResponse as Response;
use Pimple\Psr11\Container;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\UploadedFileInterface;

final class Posts
{
    private const API_NAME = 'slim4-api-skeleton';
    private const API_VERSION = '0.39.0';

    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
        //$container['upload_directory'] = __DIR__ . '/uploads';
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

    public function inputPost(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        //$directory = $this->container->get('upload_directory');
        $uploadedFiles = $request->getUploadedFiles();

        $uploadedFile = $uploadedFiles['upload_img'];
        //$upload_path = __DIR__ . '/uploads';
        $upload_path = './uploads';
        
        //print($uploadedFile->getError());
        if($uploadedFile->getError() === UPLOAD_ERR_OK){
            $filename = $this->moveUploadFile($upload_path, $uploadedFile);
            //$response->getBody()->write('Uploaded file : ' . $filename . "<br>");
            $response->withJson(['upload' => 'ok']);
        }else{
            $response->withJson(['upload' => 'fail']);
        }
        $user_idx = $data["user_idx"];
        $img_filter = $data["img_filter"];
        $content = $data["content"];
        $file_name = $filename;

        // echo $contents;
        // $email = $data->post('email');
        // $passwd = $data->post('passwd');


        $db = $this->container->get('db');

        $sql = $db->prepare("
        insert into posts (user_idx, file_name, filter_name, content, c_date) values 
        (".$user_idx.", '".$file_name."','".$img_filter."','".$content."', now())
        ");
        $rs = $sql->execute();
        //$rs = $sql->fetchAll();

        //$count = count($rs);

        if($rs){
            $res = [
                'message' => 'OK',
                'result' => $rs,
                'user_idx' => $user_idx,
            ];
        }else{
            $res = [
                'message' => 'Fail',
                'result' => $rs,
                'user_idx' => $user_idx,
            ];

        }

        return $response->withJson($res);
    }

    public function moveUploadFile(string $directory, UploadedFileInterface $uploadedFile){
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);

        $basename = bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
        return $filename;
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
    public function postAllList(Request $request, Response $response): Response
    {
        //echo 'CUSTOMERS';
        $data = $request->getQueryParams();
        $start = ($data["start"]) ? $data["start"] : 0;
        $limit = ($data["limit"]) ? $data["limit"] : 10;
        
        $db = $this->container->get('db');

        $sql_context = "Select p.*, u.name as user_name from 
        posts AS p 
        INNER JOIN users as u ON p.user_idx = u.idx
        order by idx desc 
        ";
        $sql_context .= " LIMIT $start, $limit";

        $sql = $db->prepare($sql_context);
        $sql->execute();
        
        $rs = $sql->fetchAll();
        $count = count($rs);

        if($count > 0){
            return $response->withJson($rs);
        }else{
            $result = [
                'message' => 'Fail',
                'result' => 'Fail',
                'count' => $rs,
            ];

            return $response->withJson($result);
        }

    }
    
    public function postImg(Request $request, Response $response): Response
    {
        //echo 'CUSTOMERS';
        $data = $request->getQueryParams();
        $post_idx = $data["post_idx"];
        
        $db = $this->container->get('db');

        $sql_context = "Select * from posts
        WHERE idx=".$post_idx;

        $rs = $sql = $db->prepare($sql_context);
        $sql->execute();
        
        $rs = $sql->fetch();
        $count = count($rs);

        if($count > 0){
            
            // $file = __DIR__  . "/uploads/" . $rs["file_name"];
            $file = "./uploads/" . $rs["file_name"];
            if (!file_exists($file)) {
                die("file:$file");
            }
            $image = file_get_contents($file);
            if ($image === false) {
                die("error getting image");
            }
            $body = $response->getBody();
            $body->write($image);
            return $response->withHeader('Content-Type', 'image/png');
        }else{
            $result = [
                'message' => 'Fail',
                'result' => 'Fail',
                'count' => $rs,
            ];

            return $response->withJson($result);
        }

    }
    
    public function likesProc(Request $request, Response $response): Response
    {

        $data = $request->getQueryParams();
        $post_idx = $data["post_idx"];
        $user_idx = $data["user_idx"];
        
        $db = $this->container->get('db');

        $sql_context = "Select count(*) as count from likes WHERE post_idx=".$post_idx." and user_idx=".$user_idx;

        $sql = $db->prepare($sql_context);
        $sql->execute();
        
        $rs = $sql->fetch();
        if($rs['count'] == 0) {
            $sql_con = "insert into likes values (null,".$user_idx.",".$post_idx.")";
            $sql = $db->prepare($sql_con);
            $rs = $sql->execute();
            $sql_con = "update posts set likes=likes+1 where idx=".$post_idx;
            $sql = $db->prepare($sql_con);
            $rs = $sql->execute();
            $rs_type = "plus";
        }else{
            $sql_con = "delete from likes WHERE post_idx=".$post_idx." and user_idx=".$user_idx;
            $sql = $db->prepare($sql_con);
            $rs = $sql->execute();
            $sql_con = "update posts set likes=likes-1 where idx=".$post_idx;
            $sql = $db->prepare($sql_con);
            $rs = $sql->execute();
            $rs_type = "minus";
        }

        if($rs){
            $result = [
                'message' => 'Ok',
                'result' => $rs_type,
            ];
        }else{
            $result = [
                'message' => 'Fail',
                'result' => 'Fail',
            ];
        }

        return $response->withJson($result);

    }
    
    public function test(Request $request, Response $response): Response
    {

        $data = $request->getQueryParams();
        $post_idx = $data["post_idx"];
        return $response->withJson([ 'result' => $post_idx ]);

    }
}
