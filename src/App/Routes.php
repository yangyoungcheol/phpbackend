<?php

declare(strict_types=1);

$app->get('/', 'App\Controller\Home:getHelp');
$app->post('/status', 'App\Controller\Home:getStatus');


$app->post('/login', 'App\Controller\User:userLogin');
$app->post('/help', 'App\Controller\User:getHelp');
