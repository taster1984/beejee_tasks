<?php

use Tasks\Controller;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap.php';
require __DIR__ . '/Tasks/Controller.php';
require __DIR__ . '/Tasks/Entity/Task.php';

$request = Zend\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);

$routerContainer = new Aura\Router\RouterContainer();
$map = $routerContainer->getMap();

$map->get('tasks.list.default', '/', [new Tasks\Controller,"listAction"]);
$map->get('tasks.list.page', '/page/{pid}', [new Tasks\Controller,"listAction"]);
$map->get('tasks.list.page.sort', '/page/{pid}/sort/{sid}', [new Tasks\Controller,"listAction"]);
$map->post('tasks.save','/save/{id}',[new Tasks\Controller,"saveAction"]);
$map->get('tasks.auth','/auth', [new Tasks\Controller,"authAction"]);
$map->post('tasks.auth.post','/auth', [new Tasks\Controller,"authPostAction"]);
$map->post('tasks.new.post','/new', [new Tasks\Controller,"newAction"]);
$map->post('tasks.delete','/delete/{id}', [new Tasks\Controller,"deleteAction"]);
$map->get('tasks.logout','/logout', [new Tasks\Controller,"logoutAction"]);
$map->post('tasks.complete','/complete/{id}', [new Tasks\Controller,"completeAction"]);

$matcher = $routerContainer->getMatcher();

$route = $matcher->match($request);
if (! $route) {
    echo "No route found for the request.";
    exit;
}

foreach ($route->attributes as $key => $val) {
    $request = $request->withAttribute($key, $val);
}

$callable = $route->handler;
$response = $callable($request, $entityManager);

foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header(sprintf('%s: %s', $name, $value), false);
    }
}
http_response_code($response->getStatusCode());
echo $response->getBody();