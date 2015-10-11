<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

//Request::setTrustedProxies(array('127.0.0.1'));

// TOP
$app->get('/', function () use ($app) {
    return $app['twig']->render('index.html', array());
})->bind('top');

// Login
$app->post('/login', function () use ($app) {
    $username = $app['request']->get('username');
    $password = $app['request']->get('password');

    if ($username === "test" && $password === "password") {
        $app['session']->set('user', array('username' => $username));
        return $app->redirect('/board');
    }

    return $app->redirect('/');
})->bind('login');

// Board
$app->get('/board', function(){
    return "Common!";
});

// Error
$app->error(function (\Exception $e, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/'.$code.'.html',
        'errors/'.substr($code, 0, 2).'x.html',
        'errors/'.substr($code, 0, 1).'xx.html',
        'errors/default.html',
    );

    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});
