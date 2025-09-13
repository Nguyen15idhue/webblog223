/**
 * Main JavaScript file for WebBlog223
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('WebBlog223 JS loaded');
    
    // Check if user is logged in via session storage (client-side storage)
    // This is a fallback if server-side session is not working
    const token = sessionStorage.getItem('token');
    
    if (token) {
        // Validate token by making API call
        validateToken(token);
    }
});

/**
 * Validate JWT token
 * @param {string} token - JWT token
 */
function validateToken(token) {
    // API base URL - get dynamically from window location
    const apiUrl = `${window.location.protocol}//${window.location.host}/backend`;
    
    fetch(`${apiUrl}/auth/me`, {
        method: 'GET',
        headers: {
            'Authorization': `Bearer ${token}`
        }
    })
    .then(response => {
        if (!response.ok) {
            // Token invalid, remove from session storage
            sessionStorage.removeItem('token');
            sessionStorage.removeItem('user');
        }
        return response.json();
    })
    .then(data => {
        if (!data.success) {
            // Token invalid, remove from session storage
            sessionStorage.removeItem('token');
            sessionStorage.removeItem('user');
        }
    })
    .catch(error => {
        console.error('Error validating token:', error);
    });
}

/**
 * Make API call with authorization header
 * @param {string} endpoint - API endpoint
 * @param {string} method - HTTP method
 * @param {object} data - Request data
 * @returns {Promise} - Fetch promise
 */
function apiCall(endpoint, method = 'GET', data = null) {
    // API base URL - get dynamically from window location
    const apiUrl = `${window.location.protocol}//${window.location.host}/backend`;
    
    // Get token
    const token = sessionStorage.getItem('token');
    
    // Set headers
    const headers = {
        'Content-Type': 'application/json'
    };
    
    // Add authorization header if token exists
    if (token) {
        headers.Authorization = `Bearer ${token}`;
    }
    
    // Set request options
    const options = {
        method: method,
        headers: headers
    };
    
    // Add body if data exists
    if (data !== null && (method === 'POST' || method === 'PUT')) {
        options.body = JSON.stringify(data);
    }
    
    // Make API call
    return fetch(`${apiUrl}${endpoint}`, options);
}
