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

    $sql = "SELECT t_account_id, username, mail, created_at, updated_at FROM t_account WHERE username = ? AND password = ?;";
    $user = $app['db']->fetchAssoc($sql, array($username, $password));

    if ($user) {
        $app['session']->set('user', $user);
        return $app->redirect('/board');
    }

    return $app->redirect('/');
})->bind('login');

// Logout
$app->get('/logout', function () use ($app) {
    $app['session']->set('user', null);
    return $app->redirect('/');
})->bind('logout');

// Board
$app->get('/board', function() use($app) {
    $user = $app['session']->get('user');
    return $app['twig']->render('board/index.html', array(
        'username' => $user['username'],
        't_account_id' => $user['t_account_id']
    ));
})->bind('board');

$app->get('/task', function() use($app) {
    $user = $app['session']->get('user');

    $sql = "SELECT t_task_id, t_account_id, title, description, created_at FROM t_task;";
    $task = $app['db']->fetchAll($sql);

    return $app->json($task, 200);
});

// Task Add
$app->post('/task/add', function() use($app) {
    $user = $app['session']->get('user');

    $t_account_id = $user['t_account_id'];
    $title       = $app['request']->get('title');
    $description = $app['request']->get('description');

    $sql = <<<EOF
INSERT INTO
    t_task (t_account_id, title, description, created_at, updated_at)
VALUES
    (?, ?, ?, now(), now());
EOF;

    $rowCnt = $app['db']->executeUpdate($sql, array($t_account_id, $title, $description));

    $response = new Response();
    if ($rowCnt > 0) {
        $response->setStatusCode(200, 'Success Add Task.');
    } else {
        $response->setStatusCode(400, 'Fail Add Task.');
    }

    return $response;
})->bind('taskAdd');

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
