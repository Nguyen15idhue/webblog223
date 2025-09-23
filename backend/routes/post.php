<?php
require_once __DIR__ . '/../controllers/postController.php';
require_once __DIR__ . '/../middleware/authMiddleware.php';

function handlePostRoutes($path) {
    $controller = new PostController();
    $method = $_SERVER['REQUEST_METHOD'];
    $endpoint = ltrim(str_replace('/webblog223/backend', '', $path), '/');
    
    $response = ['status' => 404, 'message' => 'Endpoint not found'];

    // --- Public Routes ---
    if ($method === 'GET' && $endpoint === 'posts') {
        $response = $controller->getAll();
    } elseif ($method === 'GET' && preg_match('/^posts\/(\d+)$/', $endpoint, $matches)) {
        $response = $controller->getById($matches[1]);
    }

    // --- Authenticated User Routes ---
    $authCallback = function($user) use (&$response, $controller, $method, $endpoint) {
        if ($method === 'POST' && $endpoint === 'posts') {
            $response = $controller->create($user['id']);
        } elseif ($method === 'PUT' && preg_match('/^posts\/(\d+)$/', $endpoint, $matches)) {
            $response = $controller->update($matches[1], $user['id'], $user['role']);
        } elseif ($method === 'DELETE' && preg_match('/^posts\/(\d+)$/', $endpoint, $matches)) {
            $response = $controller->delete($matches[1], $user['id'], $user['role']);
        } elseif ($method === 'POST' && preg_match('/^posts\/(\d+)\/like$/', $endpoint, $matches)) {
            $response = $controller->like($matches[1], $user['id']);
        }
    };

    // --- Admin Routes ---
    $adminCallback = function($user) use (&$response, $controller, $method, $endpoint) {
        if ($method === 'GET' && $endpoint === 'admin/posts') {
            $response = $controller->getAllForAdmin();
        } elseif ($method === 'PATCH' && preg_match('/^admin\/posts\/(\d+)\/status$/', $endpoint, $matches)) {
            $response = $controller->manageStatus($matches[1], $user['role']);
        }
        // Admin can also delete any post
        elseif ($method === 'DELETE' && preg_match('/^admin\/posts\/(\d+)$/', $endpoint, $matches)) {
            $response = $controller->delete($matches[1], $user['id'], $user['role']);
        }
    };

    // Route matching and authentication
    if (preg_match('/^admin\/posts/', $endpoint)) {
        AuthMiddleware::adminGuard($adminCallback);
    } elseif (preg_match('/^posts/', $endpoint) && $method !== 'GET') {
        AuthMiddleware::authenticate($authCallback);
    }

    header('Content-Type: application/json');
    http_response_code($response['status']);
    echo json_encode($response);
}
