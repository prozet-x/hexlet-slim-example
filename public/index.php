<?php
require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;

$app = AppFactory::create();

$app->get('/users', function ($request, $response) {
    return $response->write('GET /users');
});

/*$app->post('/users', function ($request, $response) {
    return $response->write('POST /users');
});*/

$app->get('/courses/{id}', function ($request, $response, array $args) {
    $id = $args['id'];
    return $response->write("Course id: {$id}");
});

$app->post('/users', function ($request, $response) {
    return $response->withStatus(302);
});

$app->run();

