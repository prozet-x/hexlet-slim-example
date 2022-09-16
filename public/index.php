<?php
require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use DI\Container;

session_start();

$container = new Container();
$container->set('renderer', function () {
    // Параметром передается базовая директория, в которой будут храниться шаблоны
    return new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');
});
$container -> set('flash', function () {
    return new \Slim\Flash\Messages();
});

$app = AppFactory::createFromContainer($container);
$app->addErrorMiddleware(true, true, true);

$users = ['mike', 'mishel', 'adel', 'keks', 'kamila'];

$router = $app->getRouteCollector()->getRouteParser();

$app->get('/users', function ($request, $response) {
	$params = [];

    $term = $request -> getQueryParam('term');
    $params['term'] = $term;

    $users = getUsers();
    $needleUsers = $term === null
		? $users
		: array_filter($users, fn ($user) => str_contains($user['name'], $term));
    $params['users'] = $needleUsers;

    $messages = $this -> get('flash') -> getMessages();
    if (!empty($messages)) {
        $params['message'] = $messages['success'][0];
    }

	return $this->get('renderer')->render($response, 'users/index.phtml', $params);
}) -> setName('users');

$app->get('/users/new', function ($request, $response) {
    $defaultValues = [
        'user' => ['name' => '', 'email' => '']
    ];
    return $this -> get('renderer') -> render($response, 'users/new.phtml', $defaultValues);
}) -> setName('NewUser');

$app -> get("/users/{id}", function ($request, $response, $args) {
    $id = (int) $args['id'];
    $users = getUsers();
    $needleUsers = array_filter($users,
    fn ($user) => $user['id'] === $id);
    if (count($needleUsers) > 0) {
        return $this->get('renderer')->render($response, 'users/show.phtml', ['id' => $needleUsers[0]['id'], 'nickname' => $needleUsers[0]['name']]);
    }
    return $this->get('renderer') -> render($response -> withStatus(404), 'users/show.phtml', ['id' => 0, 'nickname' => '']);
}) -> setName('user');

$app->post('/users', function ($request, $response) use ($router) {
    $user = $request -> getParsedBodyParam('user');
    if ($user['name'] === '' or $user['email'] === '' or $user['email'] === $user['name']) {
        return $this->get('renderer')->render($response, 'users/new.phtml', ['user' => $user]);
    }

    $user['id'] = random_int(1, 999);
    file_put_contents('users.txt', (filesize('users.txt') === 0 ? "" : PHP_EOL) . json_encode($user), FILE_APPEND);
    $this -> get('flash') -> addMessage('success', 'User was successfully added');
    return $response -> withRedirect($router ->urlFor('users'), 302);

});

function getUsers() {
    $usersAsString = explode(PHP_EOL, file_get_contents('users.txt'));
    return array_map(
        fn ($user) => json_decode($user, true),
        $usersAsString
    );
}

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

