<?php
require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use DI\Container;

$container = new Container();
$container->set('renderer', function () {
    // Параметром передается базовая директория, в которой будут храниться шаблоны
    return new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');
});
$app = AppFactory::createFromContainer($container);
$app->addErrorMiddleware(true, true, true);

$users = ['mike', 'mishel', 'adel', 'keks', 'kamila'];

$app->get('/users', function ($request, $response) use ($users){
	$term = $request -> getQueryParam('term');
	$needleUsers = $term === null
		? $users
		: array_filter($users, fn ($user) => str_contains($user, $term));
		
	return $this->get('renderer')->render($response, 'users/index.phtml', ['term' => $term, 'users' => $needleUsers]);
});

$app->get('/users/new', function ($request, $response) {
    $defaultValues = [
        'user' => ['name' => '', 'email' => '']
    ];
    return $this -> get('renderer') -> render($response, 'users/new.phtml', $defaultValues);
});

$app->post('/users', function ($request, $response) {
    $user = $request -> getParsedBodyParam('user');
    if ($user['name'] === '' or $user['email'] === '' or $user['email'] === $user['name']) {
        return $this->get('renderer')->render($response, 'users/new.phtml', ['user' => $user]);
    }

    $user['id'] = random_int(1, 999);
    file_put_contents('users.txt', json_encode($user), FILE_APPEND);
    return $response -> withRedirect('/users', 302);

});

/*use Slim\Factory\AppFactory;

$app = AppFactory::create();

$app->get('/users', function ($request, $response) {
    return $response->write('GET /users');
});

//$app->post('/users', function ($request, $response) {
//    return $response->write('POST /users');
//});

$app->get('/courses/{id}', function ($request, $response, array $args) {
    $id = $args['id'];
    return $response->write("Course id: {$id}");
});

$app->post('/users', function ($request, $response) {
    return $response->withStatus(302);
});*/

$app->run();

