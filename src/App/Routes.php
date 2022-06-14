<?php

declare(strict_types=1);

$app->get('/', 'App\Controller\Home:getHelp');
$app->get('/status', 'App\Controller\Home:getStatus');

$app->post('/help', 'App\Controller\User:getHelp');

$app->post('/login', 'App\Controller\User:userLogin');
$app->post('/signin', 'App\Controller\User:postUsers');

$app->post('/posts/upload', 'App\Controller\Posts:inputPost');
$app->get('/posts/list', 'App\Controller\Posts:postAllList');
$app->get('/post/image', 'App\Controller\Posts:postImg');

$app->get('/posts/likes', 'App\Controller\Posts:likesProc');

$app->get('/user/info', 'App\Controller\User:getUserInfo');
$app->get('/follow/check', 'App\Controller\User:getFollowChk');
$app->post('/follow/set', 'App\Controller\User:setFollow');
$app->post('/follow/list', 'App\Controller\User:getFollowList');

