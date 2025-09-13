<?php
require_once __DIR__ . '/../controllers/authController.php';
require_once __DIR__ . '/../middleware/authMiddleware.php';

/**
 * Auth Routes
 * 
 * @param string $route
 */
function authRoutes($route) {
    $authController = new AuthController();
    
    // Normalize route - handle both /auth/register and just /register
    $normalizedRoute = $route;
    if (strpos($route, '/auth/') !== 0) {
        $normalizedRoute = '/auth' . $route;
    }
    
    switch ($normalizedRoute) {
        case '/auth/register':
            $authController->register();
            break;
            
        case '/auth/login':
            $authController->login();
            break;
            
        case '/auth/me':
            // Middleware to check if user is authenticated
            AuthMiddleware::authenticate(function($user) use ($authController) {
                $authController->getCurrentUser();
            });
            break;
            
        case '/auth/profile':
            // Middleware to check if user is authenticated
            AuthMiddleware::authenticate(function($user) use ($authController) {
                $authController->updateProfile();
            });
            break;
            
        case '/auth/delete-account':
            // Middleware to check if user is authenticated
            AuthMiddleware::authenticate(function($user) use ($authController) {
                $authController->deleteAccount();
            });
            break;
            
        case '/auth/admin-only':
            // Middleware to check if user is admin
            AuthMiddleware::adminGuard(function($user) use ($authController) {
                $authController->adminOnly();
            });
            break;
            
        default:
            // Route not found
            jsonResponse([
                'success' => false,
                'message' => 'Route not found'
            ], 404);
    }
}
