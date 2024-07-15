<?php

namespace App\Application\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseFactoryInterface;
use PDO;

class RegisterAction
{
    private $pdo;
    private $responseFactory;

    public function __construct(PDO $pdo, ResponseFactoryInterface $responseFactory)
    {
        $this->pdo = $pdo;
        $this->responseFactory = $responseFactory;
    }

    public function showRegisterForm(Request $request, Response $response, array $args): Response
    {
        $html = require __DIR__ . '/../../Views/register.view.php';
        $response->getBody()->write($html);
        return $response;
    }

    public function register(Request $request, Response $response, array $args): Response
    {
        $parsedBody = $request->getParsedBody();

        $username = $parsedBody['username'] ?? null;
        $password = $parsedBody['password'] ?? null;

        if ($username === null || $password === null) {
            $response->getBody()->write("Error: Username or password is missing.");
            return $response->withStatus(400); // bad Request
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        try {
            $stmt = $this->pdo->prepare('INSERT INTO users (username, password) VALUES (:username, :password)');
            $stmt->execute(['username' => $username, 'password' => $hashedPassword]);

            $response->getBody()->write("Registration successful!");
            return $response->withStatus(201);  // Created
        } catch (\PDOException $e) {
            $response->getBody()->write("Error: " . $e->getMessage());
            return $response->withStatus(500);  // Internal Server Error
        }
    }
}
