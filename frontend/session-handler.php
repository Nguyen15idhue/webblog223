<?php
/**
 * Session handler for storing token and user data
 * This file is accessed via AJAX to store session data
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if request is POST and has JSON content type
if ($_SERVER['REQUEST_METHOD'] === 'POST' && 
    (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false)) {
    
    // Get JSON data
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    // Store token and user data in session
    if (isset($data['token'])) {
        $_SESSION['token'] = $data['token'];
    }
    
    if (isset($data['user'])) {
        $_SESSION['user'] = $data['user'];
    }
    
    // Return success response
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
} else {
    // Return error response
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
