<?php
require_once __DIR__ . '/../models/user.php';
require_once __DIR__ . '/../includes/utils.php';

class AuthService {
    private $user;
    
    public function __construct() {
        $this->user = new User();
    }
    
    /**
     * Register new user
     * 
     * @param string $username
     * @param string $email
     * @param string $password
     * @param string $role
     * @return array Response with status and data
     */
    public function register($username, $email, $password, $confirmPassword = null, $role = 'user') {
        // Validate input
        if (empty($username) || empty($email) || empty($password)) {
            return [
                'success' => false,
                'message' => 'All fields are required',
                'status' => 400
            ];
        }
        
        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Invalid email format',
                'status' => 400
            ];
        }
        
        // Validate password confirmation if provided
        if ($confirmPassword !== null && $password !== $confirmPassword) {
            return [
                'success' => false,
                'message' => 'Passwords do not match',
                'status' => 400
            ];
        }
        
        // Check if username already exists
        if ($this->user->usernameExists($username)) {
            return [
                'success' => false,
                'message' => 'Username already exists',
                'status' => 409
            ];
        }
        
        // Check if email already exists
        if ($this->user->emailExists($email)) {
            return [
                'success' => false,
                'message' => 'Email already exists',
                'status' => 409
            ];
        }
        
        // Create user
        if ($this->user->create($username, $email, $password, $role)) {
            // Check if ID is actually set
            if (!empty($this->user->id)) {
                return [
                    'success' => true,
                    'message' => 'User registered successfully',
                    'status' => 201,
                    'data' => [
                        'id' => $this->user->id,
                        'username' => $username,
                        'email' => $email,
                        'role' => $role
                    ]
                ];
            } else {
                // ID is empty but create returned true
                return [
                    'success' => true,
                    'message' => 'User registered successfully',
                    'status' => 201,
                    'data' => [
                        'username' => $username,
                        'email' => $email,
                        'role' => $role
                    ]
                ];
            }
        } else {
            // Failed to create user
            return [
                'success' => false,
                'message' => 'Failed to register user. Check server logs for details.',
                'status' => 500
            ];
        }
    }
    
    /**
     * Login user
     * 
     * @param string $username Username or email
     * @param string $password
     * @return array Response with status and data
     */
    public function login($username, $password) {
        // Validate input
        if (empty($username) || empty($password)) {
            return [
                'success' => false,
                'message' => 'Username/email and password are required',
                'status' => 400
            ];
        }
        
        // Check if input is email or username
        $isEmail = filter_var($username, FILTER_VALIDATE_EMAIL);
        
        // Get user by email or username
        if ($isEmail) {
            $userExists = $this->user->getByEmail($username);
        } else {
            $userExists = $this->user->getByUsername($username);
        }
        
        // Check if user exists
        if (!$userExists) {
            return [
                'success' => false,
                'message' => 'User not found',
                'status' => 404
            ];
        }
        
        // Verify password
        if (!$this->user->verifyPassword($password)) {
            return [
                'success' => false,
                'message' => 'Invalid password',
                'status' => 401
            ];
        }
        
        // Generate JWT token
        $token = generateJWT([
            'sub' => $this->user->id,
            'username' => $this->user->username,
            'email' => $this->user->email,
            'role' => $this->user->role
        ]);
        
        return [
            'success' => true,
            'message' => 'Login successful',
            'status' => 200,
            'data' => [
                'token' => $token,
                'user' => $this->user->toArray()
            ]
        ];
    }
    
    /**
     * Get current user from token
     * 
     * @param string $token JWT token
     * @return array Response with status and data
     */
    public function getCurrentUser($token) {
        // Verify token
        $payload = verifyJWT($token);
        
        if (!$payload) {
            return [
                'success' => false,
                'message' => 'Invalid or expired token',
                'status' => 401
            ];
        }
        
        // Get user from database
        if (!$this->user->getById($payload['sub'])) {
            return [
                'success' => false,
                'message' => 'User not found',
                'status' => 404
            ];
        }
        
        return [
            'success' => true,
            'message' => 'User retrieved successfully',
            'status' => 200,
            'data' => [
                'user' => $this->user->toArray()
            ]
        ];
    }
    
    /**
     * Check if user has admin role
     * 
     * @param string $token JWT token
     * @return array Response with status and data
     */
    public function isAdmin($token) {
        // Verify token
        $payload = verifyJWT($token);
        
        if (!$payload) {
            return [
                'success' => false,
                'message' => 'Invalid or expired token',
                'status' => 401
            ];
        }
        
        // Get user from database
        if (!$this->user->getById($payload['sub'])) {
            return [
                'success' => false,
                'message' => 'User not found',
                'status' => 404
            ];
        }
        
        // Check if user is admin
        if ($this->user->role !== 'admin') {
            return [
                'success' => false,
                'message' => 'Access denied. Admin role required.',
                'status' => 403
            ];
        }
        
        return [
            'success' => true,
            'message' => 'Admin access granted',
            'status' => 200
        ];
    }
    
    /**
     * Update user profile
     * 
     * @param string $token JWT token
     * @param string|null $username New username (optional)
     * @param string|null $email New email (optional)
     * @param string|null $currentPassword Current password (required for password change)
     * @param string|null $newPassword New password (optional)
     * @return array Response with status and data
     */
    public function updateProfile($token, $username = null, $email = null, $currentPassword = null, $newPassword = null) {
        // Verify token
        $payload = verifyJWT($token);
        
        if (!$payload) {
            return [
                'success' => false,
                'message' => 'Invalid or expired token',
                'status' => 401
            ];
        }
        
        // Get user from database
        if (!$this->user->getById($payload['sub'])) {
            return [
                'success' => false,
                'message' => 'User not found',
                'status' => 404
            ];
        }
        
        $userId = $this->user->id;
        $updateData = [];
        
        // Check if username is being updated
        if ($username !== null && $username !== $this->user->username) {
            // Check if new username already exists
            if ($username !== $this->user->username && $this->user->usernameExists($username)) {
                return [
                    'success' => false,
                    'message' => 'Username already exists',
                    'status' => 409
                ];
            }
            $updateData['username'] = $username;
        }
        
        // Check if email is being updated
        if ($email !== null && $email !== $this->user->email) {
            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return [
                    'success' => false,
                    'message' => 'Invalid email format',
                    'status' => 400
                ];
            }
            
            // Check if new email already exists
            if ($email !== $this->user->email && $this->user->emailExists($email)) {
                return [
                    'success' => false,
                    'message' => 'Email already exists',
                    'status' => 409
                ];
            }
            $updateData['email'] = $email;
        }
        
        // Check if password is being updated
        if ($newPassword !== null) {
            // Verify current password
            if ($currentPassword === null || !$this->user->verifyPassword($currentPassword)) {
                return [
                    'success' => false,
                    'message' => 'Current password is incorrect',
                    'status' => 401
                ];
            }
            $updateData['password'] = $newPassword;
        }
        
        // If nothing to update
        if (empty($updateData)) {
            return [
                'success' => false,
                'message' => 'No changes provided',
                'status' => 400
            ];
        }
        
        // Update user
        if ($this->user->update($userId, $updateData)) {
            // Get fresh user data
            $this->user->getById($userId);
            
            // Generate new token if username/email changed
            $token = null;
            if (isset($updateData['username']) || isset($updateData['email'])) {
                $token = generateJWT([
                    'sub' => $this->user->id,
                    'username' => $this->user->username,
                    'email' => $this->user->email,
                    'role' => $this->user->role
                ]);
            }
            
            $response = [
                'success' => true,
                'message' => 'Profile updated successfully',
                'status' => 200,
                'data' => [
                    'user' => $this->user->toArray()
                ]
            ];
            
            // Add token to response if it was refreshed
            if ($token) {
                $response['data']['token'] = $token;
            }
            
            return $response;
        } else {
            return [
                'success' => false,
                'message' => 'Failed to update profile',
                'status' => 500
            ];
        }
    }
    
    /**
     * Delete user account
     * 
     * @param string $token JWT token
     * @param string $password Password for verification
     * @return array Response with status and data
     */
    public function deleteAccount($token, $password) {
        // Verify token
        $payload = verifyJWT($token);
        
        if (!$payload) {
            return [
                'success' => false,
                'message' => 'Invalid or expired token',
                'status' => 401
            ];
        }
        
        // Get user from database
        if (!$this->user->getById($payload['sub'])) {
            return [
                'success' => false,
                'message' => 'User not found',
                'status' => 404
            ];
        }
        
        // Verify password
        if (!$this->user->verifyPassword($password)) {
            return [
                'success' => false,
                'message' => 'Invalid password',
                'status' => 401
            ];
        }
        
        $userId = $this->user->id;
        
        // Delete user
        if ($this->user->delete($userId)) {
            return [
                'success' => true,
                'message' => 'Account deleted successfully',
                'status' => 200
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to delete account',
                'status' => 500
            ];
        }
    }
}
