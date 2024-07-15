<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/../validator.php';
global $pdo;


$app->get('/', function (Request $request, Response $response, $args) {
    $html = view('index.view.php');
    $response->getBody()->write($html);
    return $response;
});
$app->get('/notes', function (Request $request, Response $response, $args) {
    $html = view('index.view.php');
    $response->getBody()->write($html);
    return $response;
});

$app->get('/register', function (Request $request, Response $response, $args) {
    $html = view('register.view.php');
    $response->getBody()->write($html);
    return $response;
});

$app->post('/register', function (Request $request, Response $response, $args) use ($pdo) {
    $parsedBody = $request->getParsedBody();

    $username = $parsedBody['username'] ?? null;
    $password = $parsedBody['password'] ?? null;
    $email = $parsedBody['email'] ?? null;

    $violations = validateUserData($username, $email, $password);
//    if ($username === null || $password === null || $email === null) {
//        $response->getBody()->write("Error: Username, email or password is missing.");
//        return $response->withStatus(400);
//    }
//    if (count($violations) > 0) {
//        $errors = [];
//        foreach ($violations as $violation) {
//            $errors[] = $violation->getMessage();
//        }
//        $response->getBody()->write("Validation errors: " . implode(', ', $errors));
//        return $response->withStatus(400);
//    }
    if (count($violations) > 0) {
        $messages = [];
        foreach ($violations as $violation) {
            $messages[] = $violation->getMessage();
        }
        $html = view('register.view.php', ['errors' => $messages]);
        $response->getBody()->write($html);
        return $response->withStatus(400);
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    try {
        $stmt = $pdo->prepare('INSERT INTO users (username, password, email) VALUES (:username, :password, :email)');
        $stmt->execute(['username' => $username, 'password' => $hashedPassword, 'email' => $email]);
        $_SESSION['username'] = $username;
        $html = view("index.view.php", ['username' => $username]);
        $response->getBody()->write($html);
        return $response->withStatus(201);
    } catch (PDOException $e) {
        $response->getBody()->write("Error: " . $e->getMessage());
        return $response->withStatus(500);
    }
});
$app->get('/login', function (Request $request, Response $response, $args) {
    $html = view('login.view.php');
    $response->getBody()->write($html);
    return $response;
});

$app->post('/login', function (Request $request, Response $response, $args) use ($pdo) {
    $parsedBody = $request->getParsedBody();
    $html = view('index.view.php');
    $username = $parsedBody['username'] ?? null;
    $password = $parsedBody['password'] ?? null;
    if ($username === null || $password === null) {
        $errors = "Please enter username and password";
        $html = view('login.view.php', ['errors' => $errors]);
        $response->getBody()->write($html);
        return $response->withStatus(400);
    }
    $_SESSION['username'] = $username;
//    $violations = validateUserData($username, '', $password);
//
//    if (count($violations) > 0) {
//        $errors = [];
//        foreach ($violations as $violation) {
//            $errors[] = $violation->getMessage();
//        }
//        $html = view('login.view.php', ['errors' => $errors]);
//        $response->getBody()->write($html);
//        return $response->withStatus(400);
//    }
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :username');
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $username;
        $html = view("index.view.php", ['username' => $username]);
        $response->getBody()->write($html);
        return $response->withStatus(200);
    } else {
        $errors = "Username or password is incorrect";
        $html = view('login.view.php', ['errors' => $errors]);
        $response->getBody()->write($html);
        return $response->withStatus(401);
    }
});

$app->get('/logout', function (Request $request, Response $response, $args) {
    logout();
    $html = view('index.view.php');
    $response->getBody()->write($html);
    return $response->withStatus(200);
});
