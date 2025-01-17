<?php

use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();

$items = [];

// Get all items
$app->get('/items', function (Request $request, Response $response) use (&$items) {
    $response->getBody()->write(json_encode(array_values($items)));
    return $response->withHeader('Content-Type', 'application/json');
});

// Get a single item
$app->get('/items/{id}', function (Request $request, Response $response, $args) use (&$items) {
    $id = $args['id'];
    if (!isset($items[$id])) {
        return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
    }
    $response->getBody()->write(json_encode($items[$id]));
    return $response->withHeader('Content-Type', 'application/json');
});

// Create a new item
$app->post('/items', function (Request $request, Response $response) use (&$items) {
    $data = json_decode($request->getBody()->getContents(), true);
    if (isset($items[$data['id']])) {
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }
    $items[$data['id']] = $data;
    $response->getBody()->write(json_encode($data));
    return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
});

// Update an item
$app->put('/items/{id}', function (Request $request, Response $response, $args) use (&$items) {
    $id = $args['id'];
    if (!isset($items[$id])) {
        return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
    }
    $data = json_decode($request->getBody()->getContents(), true);
    $data['id'] = $id; // Ensure ID consistency
    $items[$id] = $data;
    $response->getBody()->write(json_encode($data));
    return $response->withHeader('Content-Type', 'application/json');
});

// Delete an item
$app->delete('/items/{id}', function (Request $request, Response $response, $args) use (&$items) {
    $id = $args['id'];
    if (!isset($items[$id])) {
        return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
    }
    unset($items[$id]);
    return $response->withStatus(204)->withHeader('Content-Type', 'application/json');
});

$app->run();

?>
