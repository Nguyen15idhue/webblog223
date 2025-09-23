<?php
/**
 * Backend API entry point
 * This file handles all incoming requests to the backend API
 */

// Include configuration
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/utils.php';

// Handle CORS
header('Access-Control-Allow-Origin: http://webblog223.test');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Include routes
require_once __DIR__ . '/routes/auth.php';
require_once __DIR__ . '/routes/comment.php'; 
require_once __DIR__ . '/routes/report.php';

// Get request path
$requestPath = getRequestPath();

// More flexible path handling for different server configurations
$pathParts = explode('/', trim($requestPath, '/'));
$basePathIndex = array_search('backend', $pathParts);

if ($basePathIndex !== false && isset($pathParts[$basePathIndex + 1])) {
    // Extract the route after 'backend'
    $route = '/' . implode('/', array_slice($pathParts, $basePathIndex + 1));
} else {
    // Default route (just /backend)
    $route = '';
}

// Route requests
if (strpos($route, '/auth') === 0) {
    authRoutes($route);
} elseif (strpos($route, '/comments') === 0) {
    commentRoutes($route); 
}elseif (strpos($route, '/comment-reports') === 0) {
    reportRoutes($route); 
}else {
    // Default route - API info
    jsonResponse([
        'success' => true,
        'message' => 'WebBlog223 API',
        'version' => '1.0.0',
        'endpoints' => [
            '/auth/register'      => 'Register a new user',
            '/auth/login'         => 'Login user',
            '/auth/me'            => 'Get current user info',
            '/auth/profile'       => 'Update user profile',
            '/auth/delete-account'=> 'Delete user account',
            '/auth/admin-only'    => 'Admin only endpoint',
            '/comments/{post_id}' => 'Get comments for a post',
            '/comments (POST)'    => 'Create new comment (pending)',
            '/comments/{id} (PUT)' => 'Update comment',
            '/comments/{id} (DELETE)' => 'Delete comment',
            '/comments/{id}/approve (PATCH)' => 'Approve comment (admin)',
            '/comments/{id}/reject (PATCH)'  => 'Reject comment (admin)',
            '/comment-reports (POST)' => 'Create new report',
            '/comment-reports/{comment_id}' => 'Get reports for a comment',
            '/comment-reports (GET)' => 'Get all reports (admin)',
            '/comment-reports/{id} (DELETE)' => 'Delete report',
        ]
    ]);
}
