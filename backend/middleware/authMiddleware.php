<?php
require_once __DIR__ . '/../includes/utils.php';
require_once __DIR__ . '/../models/user.php';

class AuthMiddleware {
    /**
     * Check if user is authenticated
     * 
     * @return array|boolean User data if authenticated, false otherwise
     */
    public static function isAuthenticated() {
        // Get token
        $token = getBearerToken();
        
        if (!$token) {
            return false;
        }
        
        // Verify token
        $payload = verifyJWT($token);
        
        if (!$payload) {
            return false;
        }
        
        // Check if user exists
        $user = new User();
        if (!$user->getById($payload['sub'])) {
            return false;
        }
        
        return $user->toArray();
    }
    
    /**
     * Check if user is admin
     * 
     * @return boolean
     */
    public static function isAdmin() {
        $userData = self::isAuthenticated();
        
        if (!$userData) {
            return false;
        }
        
        return $userData['role'] === 'admin';
    }
    
    /**
     * Authentication guard middleware
     * 
     * @param callable $next Function to call if authenticated
     * @return void
     */
    public static function authenticate($next) {
        $userData = self::isAuthenticated();
        
        if (!$userData) {
            jsonResponse([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        
        // Call next function with user data
        $next($userData);
    }
    
    /**
     * Admin guard middleware
     * 
     * @param callable $next Function to call if admin
     * @return void
     */
    public static function adminGuard($next) {
        $userData = self::isAuthenticated();
        
        if (!$userData) {
            jsonResponse([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        
        if ($userData['role'] !== 'admin') {
            jsonResponse([
                'success' => false,
                'message' => 'Access denied. Admin role required.'
            ], 403);
        }
        
        // Call next function with user data
        $next($userData);
    }
}
