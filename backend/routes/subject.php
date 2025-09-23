<?php
require_once __DIR__ . '/../controllers/subjectController.php';
require_once __DIR__ . '/../middleware/authMiddleware.php';

class SubjectRoutes {
    private $subjectController;
    
    public function __construct() {
        $this->subjectController = new SubjectController();
    }
    
    public function handleRequest($method, $endpoint) {
        // Public routes
        if ($method === 'GET') {
            // Get all subjects
            if ($endpoint === 'subjects') {
                return $this->subjectController->getAll();
            }
            
            // Get subject by ID
            if (strpos($endpoint, 'subjects/') === 0) {
                $_GET['id'] = substr($endpoint, 9); // Extract ID from URL
                return $this->subjectController->getById();
            }
            
            // Search subjects
            if ($endpoint === 'subjects/search') {
                return $this->subjectController->search();
            }
        }
        
        // For non-GET methods, we'll handle admin authentication in a closure
        // and return the result from inside that closure
        $result = ['status' => 404, 'message' => 'Endpoint not found'];
        $requestHandled = false;
        
        // Handle non-GET requests with authentication
        if ($method === 'POST' && $endpoint === 'subjects') {
            // For subject creation (admin only)
            AuthMiddleware::adminGuard(function($user) use (&$result, &$requestHandled) {
                $result = $this->subjectController->create();
                $requestHandled = true;
            });
        }
        else if ($method === 'PUT' && strpos($endpoint, 'subjects/') === 0) {
            // For subject update (admin only)
            AuthMiddleware::adminGuard(function($user) use (&$result, &$requestHandled, $endpoint) {
                // Get data from request body
                $data = json_decode(file_get_contents('php://input'), true) ?? [];
                $data['id'] = substr($endpoint, 9); // Extract ID from URL
                $_REQUEST = $data; // Make data available to controller
                $result = $this->subjectController->update();
                $requestHandled = true;
            });
        }
        else if ($method === 'DELETE' && strpos($endpoint, 'subjects/') === 0) {
            // For subject deletion (admin only)
            AuthMiddleware::adminGuard(function($user) use (&$result, &$requestHandled, $endpoint) {
                // Get data from request body
                $data = json_decode(file_get_contents('php://input'), true) ?? [];
                $data['id'] = substr($endpoint, 9); // Extract ID from URL
                $_REQUEST = $data; // Make data available to controller
                $result = $this->subjectController->delete();
                $requestHandled = true;
            });
        }
        else if ($method === 'PATCH' && strpos($endpoint, 'subjects/status/') === 0) {
            // For toggling subject status (admin only)
            AuthMiddleware::adminGuard(function($user) use (&$result, &$requestHandled, $endpoint) {
                // Get data from request body
                $data = json_decode(file_get_contents('php://input'), true) ?? [];
                $data['id'] = substr($endpoint, 16); // Extract ID from URL
                $_REQUEST = $data; // Make data available to controller
                $result = $this->subjectController->toggleStatus();
                $requestHandled = true;
            });
        }
        
        // Return the result - if no handler matched and executed, this will be a 404
        return $result;
    }
}