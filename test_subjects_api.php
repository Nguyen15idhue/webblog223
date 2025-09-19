<?php
/**
 * Simple test script for the Subject API
 * 
 * This script demonstrates how to interact with the Subject API endpoints
 */

// Base URL for API
$baseUrl = 'http://webblog223.test/backend';

// Function to make API requests
function apiRequest($endpoint, $method = 'GET', $data = [], $token = null) {
    global $baseUrl;
    
    $url = $baseUrl . $endpoint;
    
    $ch = curl_init($url);
    
    $headers = ['Content-Type: application/json'];
    
    // Add token if provided
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    if (!empty($data)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

// Login to get admin token
function getAdminToken() {
    $loginData = [
        'email' => 'admin@example.com',  // Update with your admin credentials
        'password' => 'admin123'         // Update with your admin password
    ];
    
    $response = apiRequest('/auth/login', 'POST', $loginData);
    
    if ((isset($response['success']) && $response['success']) || 
        (isset($response['status']) && $response['status'] >= 200 && $response['status'] < 300)) {
        return $response['data']['token'] ?? $response['token'];
    }
    
    echo "Failed to login: " . ($response['message'] ?? 'Unknown error') . "\n";
    return null;
}

// Tests

echo "=== Subjects API Test ===\n\n";

// Step 1: Get admin token
echo "Step 1: Getting admin token...\n";
$adminToken = getAdminToken();
if (!$adminToken) {
    echo "Error: Could not get admin token. Make sure admin account exists and credentials are correct.\n";
    exit(1);
}
echo "Success! Admin token received.\n\n";

// Step 2: Get all subjects (public endpoint)
echo "Step 2: Getting all subjects...\n";
$allSubjects = apiRequest('/subjects');
echo "Response: " . json_encode($allSubjects, JSON_PRETTY_PRINT) . "\n\n";

// Step 3: Create a new subject (admin only)
echo "Step 3: Creating a new subject...\n";
$newSubjectData = [
    'subject_name' => 'Test Subject ' . time(),
    'content_subject' => 'This is a test subject created via the API test script',
    'status' => 1
];
$createResponse = apiRequest('/subjects', 'POST', $newSubjectData, $adminToken);
echo "Response: " . json_encode($createResponse, JSON_PRETTY_PRINT) . "\n\n";

if ((isset($createResponse['status']) && $createResponse['status'] == 201) || 
    (isset($createResponse['success']) && $createResponse['success'])) {
    $newSubjectId = $createResponse['data']['id'] ?? $createResponse['id'];
    
    // Step 4: Get the subject by ID
    echo "Step 4: Getting subject by ID...\n";
    $getSubject = apiRequest('/subjects/' . $newSubjectId);
    echo "Response: " . json_encode($getSubject, JSON_PRETTY_PRINT) . "\n\n";
    
    // Step 5: Update the subject
    echo "Step 5: Updating the subject...\n";
    $updateData = [
        'subject_name' => 'Updated Subject ' . time(),
        'content_subject' => 'This subject was updated via the API test script'
    ];
    $updateResponse = apiRequest('/subjects/' . $newSubjectId, 'PUT', $updateData, $adminToken);
    echo "Response: " . json_encode($updateResponse, JSON_PRETTY_PRINT) . "\n\n";
    
    // Step 6: Toggle subject status
    echo "Step 6: Toggling subject status...\n";
    $toggleResponse = apiRequest('/subjects/status/' . $newSubjectId, 'PATCH', [], $adminToken);
    echo "Response: " . json_encode($toggleResponse, JSON_PRETTY_PRINT) . "\n\n";
    
    // Step 7: Search for subjects
    echo "Step 7: Searching for subjects...\n";
    $searchTerm = 'updated';
    $searchResponse = apiRequest('/subjects/search?keyword=' . urlencode($searchTerm));
    echo "Response: " . json_encode($searchResponse, JSON_PRETTY_PRINT) . "\n\n";
    
    // Step 8: Delete the subject
    echo "Step 8: Deleting the subject...\n";
    $deleteResponse = apiRequest('/subjects/' . $newSubjectId, 'DELETE', [], $adminToken);
    echo "Response: " . json_encode($deleteResponse, JSON_PRETTY_PRINT) . "\n\n";
} else {
    echo "Error: Could not create test subject.\n";
}

echo "=== Test Completed ===\n";