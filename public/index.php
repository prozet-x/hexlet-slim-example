<?php
require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use DI\Container;
use Slim\Middleware\MethodOverrideMiddleware;

/*
const EMPTY_USER = ['name' => '', 'body' => ''];

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
$app->add(MethodOverrideMiddleware::class);

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
    $defaultValues = ['user' => EMPTY_USER];
    return $this -> get('renderer') -> render($response, 'users/new.phtml', $defaultValues);
}) -> setName('NewUser');

$app -> get("/users/{id}", function ($request, $response, $args) {
    $id = (int) $args['id'];
    $needleUser = getUser($id);
    if ($needleUser) {
        return $this->get('renderer')->render($response, 'users/show.phtml', ['id' => $needleUser['id'], 'nickname' => $needleUser['name']]);
    }
    return $this->get('renderer') -> render($response -> withStatus(404), 'users/show.phtml', ['id' => 0, 'nickname' => '']);
}) -> setName('user');

$app -> get("/users/{id}/edit", function ($request, $response, $args) {
    $id = (int) $args['id'];
    $needleUser = getUser($id);
    if ($needleUser) {
        return $this->get('renderer')->render($response, 'users/edit.phtml', ['user' => $needleUser]);
    }
    return $this->get('renderer') -> render($response -> withStatus(404), 'users/edit.phtml', ['id' => 0, 'nickname' => '']);
}) -> setName('userEdit');

$app -> delete('/users/{id}', function ($req, $resp, $args) use ($router) {
    $id = $args['id'];
    deleteUser((int) $id);
    $this -> get('flash') -> addMessage('success', 'Post has been deleted');
    return $resp -> withRedirect($router -> urlFor('users'), );
});

$app->post('/users', function ($request, $response) use ($router) {
    $user = $request -> getParsedBodyParam('user');
    $errors = validate($user);
    if (count($errors) > 0) {
        return $this->get('renderer')->render($response -> withStatus(422), 'users/new.phtml', ['user' => $user, 'errors' => $errors]);
    }
    $user['id'] = random_int(1, 999999);
    file_put_contents('users.txt', (filesize('users.txt') === 0 ? "" : PHP_EOL) . json_encode($user), FILE_APPEND);
    $this -> get('flash') -> addMessage('success', 'User was successfully added');
    return $response -> withRedirect($router ->urlFor('users'), 302);
});

$app -> post('/users/{id}/edit', function ($req, $resp, $args) use ($router) {
    $updatedUser = $req -> getParsedBodyParam('user', EMPTY_USER);
    $updatedUser['id'] = (int) $args['id'];
    $errors = validate($updatedUser);
    if (count($errors) > 0) {
        return $this->get('renderer')->render($resp -> withStatus(422), 'users/edit.phtml', ['user' => $updatedUser, 'errors' => $errors]);
    }
    if (updateUser($updatedUser)) {
        return $resp -> withRedirect($router -> urlFor('users'), 302);
    }
});

function deleteUser($id)
{
    $users = getUsers();
    $filteredUsers = array_filter($users, fn ($user) => $user['id'] !== $id);
    $newUsers = array_map(fn ($user) => json_encode($user), $filteredUsers);
    return file_put_contents('users.txt', implode(PHP_EOL, $newUsers));
}

function updateUser($newUser)
{
    $users = getUsers();
    $newUsers = array_map(function ($user) use ($newUser) {
        if ($user['id'] === $newUser['id']) {
            $user['name'] = $newUser['name'];
            $user['email'] = $newUser['email'];
        }
        return json_encode($user);
    },
    $users);
    return file_put_contents('users.txt', implode(PHP_EOL, $newUsers));
}

function getUsers()
{
    return array_map(
        fn ($user) => json_decode(trim($user), true),
        file('users.txt', FILE_IGNORE_NEW_LINES)
    );
}

function getUser($id)
{
    $needleUsers = array_values(array_filter(getUsers(), fn ($user) => $user['id'] === $id));
    return count($needleUsers) > 0 ? $needleUsers[0] : false;
}

function validate($user): array
{
    $errors = [];
    if ($user['name'] === '' or $user['email'] === '') {
        $errors[] = 'All fields are required';
    }
    if (strlen($user['name']) < 5) {
        $errors[] = 'Nickname must be grater that 4 characters';
    }
    if (strlen($user['email']) < 5) {
        $errors[] = 'Email must be grater that 4 characters';
    }
    return $errors;
}

$app->run();
*/

const EMPTY_USER = ['name' => '', 'body' => ''];

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
$app->add(MethodOverrideMiddleware::class);

$router = $app->getRouteCollector()->getRouteParser();

$app->get('/users', function ($request, $response) {
    $params = [];

    $term = $request -> getQueryParam('term');
    $params['term'] = $term;

    //$users = getUsers();
    $users = json_decode($request -> cookies['users'] ?? '{}');
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
    $defaultValues = ['user' => EMPTY_USER];
    return $this -> get('renderer') -> render($response, 'users/new.phtml', $defaultValues);
}) -> setName('NewUser');

$app->post('/users', function ($req, $resp) use ($router) {
    $user = $req -> getParsedBodyParam('user');
    $errors = validate($user);
    if (count($errors) > 0) {
        return $this->get('renderer')->render($resp -> withStatus(422), 'users/new.phtml', ['user' => $user, 'errors' => $errors]);
    }
    $user['id'] = random_int(1, 999999);
    $users = json_decode($req -> getCookieParam('users', json_encode([])));
    $users[] = $user;
    $usersEncoded = json_encode($users);
    setcookie('users', $usersEncoded);
    $this -> get('flash') -> addMessage('success', 'User was successfully added');
    //$this -> setCookie('users', $usersEncoded);
    return $resp -> withHeader('Set-Cookie', "users={$usersEncoded}") -> withRedirect($router ->urlFor('users'), 302);
});

function validate($user): array
{
    $errors = [];
    if ($user['name'] === '' or $user['email'] === '') {
        $errors[] = 'All fields are required';
    }
    if (strlen($user['name']) < 5) {
        $errors[] = 'Nickname must be grater that 4 characters';
    }
    if (strlen($user['email']) < 5) {
        $errors[] = 'Email must be grater that 4 characters';
    }
    return $errors;
}

$app->run();

