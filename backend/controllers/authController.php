<?php
require_once __DIR__ . '/../services/authService.php';
require_once __DIR__ . '/../includes/utils.php';

class AuthController {
    private $authService;
    
    public function __construct() {
        $this->authService = new AuthService();
    }
    
    /**
     * Handle user registration
     */
    public function register() {
        // Only allow POST requests
        if (getRequestMethod() !== 'POST') {
            jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
        }
        
        // Get request data
        $data = getJsonBody();
        
        // Extract data
        $username = isset($data['username']) ? sanitizeInput($data['username']) : '';
        $email = isset($data['email']) ? sanitizeInput($data['email']) : '';
        $password = isset($data['password']) ? $data['password'] : '';
        $confirmPassword = isset($data['confirmPassword']) ? $data['confirmPassword'] : '';
        
        // Register user
        $result = $this->authService->register($username, $email, $password, $confirmPassword);
        
        // Return response
        jsonResponse($result, $result['status']);
    }
    
    /**
     * Handle user login
     */
    public function login() {
        // Only allow POST requests
        if (getRequestMethod() !== 'POST') {
            jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
        }
        
        // Get request data
        $data = getJsonBody();
        
        // Extract data
        $username = isset($data['username']) ? sanitizeInput($data['username']) : '';
        $password = isset($data['password']) ? $data['password'] : '';
        
        // Login user
        $result = $this->authService->login($username, $password);
        
        // Return response
        jsonResponse($result, $result['status']);
    }
    
    /**
     * Get current user
     */
    public function getCurrentUser() {
        // Only allow GET requests
        if (getRequestMethod() !== 'GET') {
            jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
        }
        
        // Get token from request
        $token = getBearerToken();
        
        if (!$token) {
            jsonResponse([
                'success' => false,
                'message' => 'No token provided'
            ], 401);
        }
        
        // Get user from token
        $result = $this->authService->getCurrentUser($token);
        
        // Return response
        jsonResponse($result, $result['status']);
    }
    
    /**
     * Admin only endpoint
     */
    public function adminOnly() {
        // Only allow GET requests
        if (getRequestMethod() !== 'GET') {
            jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
        }
        
        // Get token from request
        $token = getBearerToken();
        
        if (!$token) {
            jsonResponse([
                'success' => false,
                'message' => 'No token provided'
            ], 401);
        }
        
        // Check if user is admin
        $result = $this->authService->isAdmin($token);
        
        if (!$result['success']) {
            jsonResponse($result, $result['status']);
        }
        
        // Return admin data
        jsonResponse([
            'success' => true,
            'message' => 'Welcome to admin area',
            'data' => [
                'adminInfo' => 'This is a protected admin endpoint',
                'timestamp' => time()
            ]
        ], 200);
    }
    
    /**
     * Update user profile
     */
    public function updateProfile() {
        // Only allow PUT requests
        if (getRequestMethod() !== 'PUT' && getRequestMethod() !== 'POST') {
            jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
        }
        
        // Get token from request
        $token = getBearerToken();
        
        if (!$token) {
            jsonResponse([
                'success' => false,
                'message' => 'No token provided'
            ], 401);
        }
        
        // Get request data
        $data = getJsonBody();
        
        // Extract data
        $username = isset($data['username']) ? sanitizeInput($data['username']) : null;
        $email = isset($data['email']) ? sanitizeInput($data['email']) : null;
        $currentPassword = isset($data['currentPassword']) ? $data['currentPassword'] : null;
        $newPassword = isset($data['newPassword']) ? $data['newPassword'] : null;
        
        // Update user
        $result = $this->authService->updateProfile($token, $username, $email, $currentPassword, $newPassword);
        
        // Return response
        jsonResponse($result, $result['status']);
    }
    
    /**
     * Delete user account
     */
    public function deleteAccount() {
        // Only allow DELETE requests or POST with _method=DELETE
        if (getRequestMethod() !== 'DELETE' && 
            !(getRequestMethod() === 'POST' && isset($_POST['_method']) && $_POST['_method'] === 'DELETE')) {
            jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
        }
        
        // Get token from request
        $token = getBearerToken();
        
        if (!$token) {
            jsonResponse([
                'success' => false,
                'message' => 'No token provided'
            ], 401);
        }
        
        // Get request data
        $data = getJsonBody();
        
        // Extract password for verification
        $password = isset($data['password']) ? $data['password'] : '';
        
        // Delete user
        $result = $this->authService->deleteAccount($token, $password);
        
        // Return response
        jsonResponse($result, $result['status']);
    }
}
