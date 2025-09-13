<?php
// Lấy URL cơ sở động
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = 'webblog223.test';
$base_url = $protocol . $host;
$api_url = $protocol . $host . '/backend';

// Lấy token đăng nhập từ session nếu có
session_start();
$token = $_SESSION['token'] ?? '';
$userData = $_SESSION['user'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebBlog223 API Tester</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .response-container {
            max-height: 500px;
            overflow-y: auto;
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
            margin-top: 20px;
        }
        .form-container {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .nav-pills .nav-link.active {
            background-color: #0d6efd;
        }
        .endpoint-description {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 15px;
        }
        pre {
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .card {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-4 mb-5">
        <h1 class="mb-4">WebBlog223 API Tester</h1>
        
        <div class="row">
            <div class="col-md-12">
                <!-- Authentication Status -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Authentication Status</h5>
                        <?php if ($token): ?>
                            <button class="btn btn-sm btn-danger" id="logout-btn">Logout</button>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php if ($token): ?>
                            <div class="alert alert-success">
                                <p><strong>Logged in as:</strong> <?php echo htmlspecialchars($userData['username']); ?> (<?php echo htmlspecialchars($userData['email']); ?>)</p>
                                <p><strong>Role:</strong> <?php echo htmlspecialchars($userData['role']); ?></p>
                                <p><strong>User ID:</strong> <?php echo htmlspecialchars($userData['id']); ?></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>Your JWT Token:</strong></label>
                                <textarea class="form-control" rows="3" readonly id="jwt-token"><?php echo $token; ?></textarea>
                            </div>
                            <button class="btn btn-sm btn-secondary copy-btn" data-clipboard-target="#jwt-token">Copy Token</button>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <p>You are not logged in. Use the login endpoint to get a JWT token.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Nav tabs -->
                <ul class="nav nav-pills mb-3" id="api-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="public-tab" data-bs-toggle="pill" data-bs-target="#public" type="button" role="tab">Public Endpoints</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="authenticated-tab" data-bs-toggle="pill" data-bs-target="#authenticated" type="button" role="tab">Authenticated Endpoints</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="admin-tab" data-bs-toggle="pill" data-bs-target="#admin" type="button" role="tab">Admin Endpoints</button>
                    </li>
                </ul>

                <!-- Tab content -->
                <div class="tab-content">
                    <!-- Public Endpoints -->
                    <div class="tab-pane fade show active" id="public" role="tabpanel" aria-labelledby="public-tab">
                        <!-- Register Form -->
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Register</h5>
                            </div>
                            <div class="card-body">
                                <p class="endpoint-description">POST /backend/auth/register</p>
                                <form id="register-form" class="mb-3">
                                    <div class="mb-3">
                                        <label for="register-username" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="register-username" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="register-email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="register-email" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="register-password" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="register-password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="register-confirm-password" class="form-label">Confirm Password</label>
                                        <input type="password" class="form-control" id="register-confirm-password" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Register</button>
                                </form>
                                <div class="response-container d-none" id="register-response"></div>
                            </div>
                        </div>

                        <!-- Login Form -->
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">Login</h5>
                            </div>
                            <div class="card-body">
                                <p class="endpoint-description">POST /backend/auth/login</p>
                                <form id="login-form" class="mb-3">
                                    <div class="mb-3">
                                        <label for="login-username" class="form-label">Username or Email</label>
                                        <input type="text" class="form-control" id="login-username" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="login-password" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="login-password" required>
                                    </div>
                                    <button type="submit" class="btn btn-success">Login</button>
                                </form>
                                <div class="response-container d-none" id="login-response"></div>
                            </div>
                        </div>

                        <!-- Root API Info -->
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">API Info</h5>
                            </div>
                            <div class="card-body">
                                <p class="endpoint-description">GET /backend</p>
                                <button class="btn btn-info" id="api-info-btn">Get API Info</button>
                                <div class="response-container d-none" id="api-info-response"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Authenticated Endpoints -->
                    <div class="tab-pane fade" id="authenticated" role="tabpanel" aria-labelledby="authenticated-tab">
                        <!-- Current User -->
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Get Current User</h5>
                            </div>
                            <div class="card-body">
                                <p class="endpoint-description">GET /backend/auth/me</p>
                                <button class="btn btn-primary" id="me-btn">Get Current User</button>
                                <div class="response-container d-none" id="me-response"></div>
                            </div>
                        </div>

                        <!-- Update Profile -->
                        <div class="card">
                            <div class="card-header bg-warning">
                                <h5 class="mb-0">Update Profile</h5>
                            </div>
                            <div class="card-body">
                                <p class="endpoint-description">PUT /backend/auth/profile</p>
                                <form id="update-profile-form" class="mb-3">
                                    <div class="mb-3">
                                        <label for="update-username" class="form-label">New Username</label>
                                        <input type="text" class="form-control" id="update-username">
                                    </div>
                                    <div class="mb-3">
                                        <label for="update-email" class="form-label">New Email</label>
                                        <input type="email" class="form-control" id="update-email">
                                    </div>
                                    <hr>
                                    <h6>Change Password (Optional)</h6>
                                    <div class="mb-3">
                                        <label for="current-password" class="form-label">Current Password</label>
                                        <input type="password" class="form-control" id="current-password">
                                    </div>
                                    <div class="mb-3">
                                        <label for="new-password" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="new-password">
                                    </div>
                                    <button type="submit" class="btn btn-warning">Update Profile</button>
                                </form>
                                <div class="response-container d-none" id="update-profile-response"></div>
                            </div>
                        </div>

                        <!-- Delete Account -->
                        <div class="card">
                            <div class="card-header bg-danger text-white">
                                <h5 class="mb-0">Delete Account</h5>
                            </div>
                            <div class="card-body">
                                <p class="endpoint-description">DELETE /backend/auth/delete-account</p>
                                <form id="delete-account-form" class="mb-3">
                                    <div class="mb-3">
                                        <label for="delete-password" class="form-label">Confirm Password</label>
                                        <input type="password" class="form-control" id="delete-password" required>
                                    </div>
                                    <button type="submit" class="btn btn-danger">Delete My Account</button>
                                </form>
                                <div class="response-container d-none" id="delete-account-response"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Admin Endpoints -->
                    <div class="tab-pane fade" id="admin" role="tabpanel" aria-labelledby="admin-tab">
                        <!-- Admin Only -->
                        <div class="card">
                            <div class="card-header bg-dark text-white">
                                <h5 class="mb-0">Admin Only Endpoint</h5>
                            </div>
                            <div class="card-body">
                                <p class="endpoint-description">GET /backend/auth/admin-only</p>
                                <button class="btn btn-dark" id="admin-only-btn">Access Admin Area</button>
                                <div class="response-container d-none" id="admin-only-response"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.11/dist/clipboard.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const apiUrl = '<?php echo $api_url; ?>';
            const baseUrl = '<?php echo $base_url; ?>';
            const token = '<?php echo $token; ?>';

            // Initialize clipboard
            new ClipboardJS('.copy-btn');

            // Function to format and display response
            function displayResponse(responseElem, data, success = true) {
                const formattedData = JSON.stringify(data, null, 2);
                responseElem.innerHTML = `<pre class="${success ? 'text-success' : 'text-danger'}">${formattedData}</pre>`;
                responseElem.classList.remove('d-none');
                
                // Add debugging information if there was an error
                if (!success && data.message === 'Failed to fetch') {
                    responseElem.innerHTML += `
                    <div class="alert alert-warning mt-3">
                        <h5>Troubleshooting Tips:</h5>
                        <ol>
                            <li>Make sure "webblog223.test" is in your hosts file (C:\\Windows\\System32\\drivers\\etc\\hosts) and points to 127.0.0.1</li>
                            <li>Check if Laragon is running properly</li>
                            <li>Try opening <a href="http://webblog223.test/hostname_checker.php" target="_blank">hostname_checker.php</a> to verify configuration</li>
                            <li>Check browser console (F12) for more detailed error messages</li>
                        </ol>
                    </div>`;
                }
            }

            // Function to make API calls
            async function callApi(endpoint, method, data = null, authRequired = false) {
                try {
                    const options = {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        credentials: 'include',
                        mode: 'cors'
                    };

                    // Add auth token if required
                    if (authRequired && token) {
                        options.headers.Authorization = `Bearer ${token}`;
                    }

                    // Add body if data is provided
                    if (data) {
                        options.body = JSON.stringify(data);
                    }

                    console.log('Making API call to:', endpoint, options);
                    const response = await fetch(endpoint, options);
                    
                    if (!response.ok) {
                        const errorText = await response.text();
                        console.error('API Response not OK:', response.status, errorText);
                        return { success: false, message: `HTTP Error: ${response.status}`, details: errorText };
                    }
                    
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        return await response.json();
                    } else {
                        const text = await response.text();
                        console.warn('Non-JSON response:', text);
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            return { success: false, message: 'Invalid JSON response', responseText: text };
                        }
                    }
                } catch (error) {
                    console.error('API Error:', error);
                    return { success: false, message: error.message };
                }
            }

            // Store token in session handler
            async function storeTokenInSession(token, user) {
                try {
                    await fetch(`http://webblog223.test/frontend/session-handler.php`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            token: token,
                            user: user
                        })
                    });
                    // Reload the page to update auth status
                    window.location.reload();
                } catch (error) {
                    console.error('Session error:', error);
                }
            }

            // Register form
            document.getElementById('register-form').addEventListener('submit', async function(e) {
                e.preventDefault();
                const responseElem = document.getElementById('register-response');
                responseElem.innerHTML = '<div class="alert alert-info">Processing registration...</div>';
                responseElem.classList.remove('d-none');
                
                const data = {
                    username: document.getElementById('register-username').value,
                    email: document.getElementById('register-email').value,
                    password: document.getElementById('register-password').value,
                    confirmPassword: document.getElementById('register-confirm-password').value
                };
                
                console.log('Submitting registration form with data:', { ...data, password: '***', confirmPassword: '***' });
                
                // Try with different URL format
                const registerUrl = 'http://webblog223.test/backend/auth/register';
                console.log('Using register URL:', registerUrl);
                
                // Try with fetch first
                try {
                    const response = await callApi(registerUrl, 'POST', data);
                    displayResponse(responseElem, response, response.success);
                } catch (error) {
                    console.error("Fetch failed, trying XMLHttpRequest as fallback", error);
                    
                    // Fallback to XMLHttpRequest if fetch fails
                    responseElem.innerHTML += '<div class="alert alert-info mt-2">Fetch failed, trying alternative method...</div>';
                    
                    // Use XMLHttpRequest as a fallback
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', registerUrl, true);
                    xhr.setRequestHeader('Content-Type', 'application/json');
                    
                    xhr.onload = function() {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            displayResponse(responseElem, response, response.success);
                        } catch (e) {
                            displayResponse(responseElem, {
                                success: false,
                                message: 'Failed to parse response',
                                responseText: xhr.responseText,
                                status: xhr.status
                            }, false);
                        }
                    };
                    
                    xhr.onerror = function() {
                        displayResponse(responseElem, {
                            success: false,
                            message: 'XMLHttpRequest failed',
                            status: xhr.status,
                            statusText: xhr.statusText
                        }, false);
                    };
                    
                    xhr.send(JSON.stringify(data));
                }
                
                // If registration was successful, clear the form
                if (response.success) {
                    document.getElementById('register-form').reset();
                }
            });

            // Login form
            document.getElementById('login-form').addEventListener('submit', async function(e) {
                e.preventDefault();
                const responseElem = document.getElementById('login-response');
                
                const data = {
                    username: document.getElementById('login-username').value,
                    password: document.getElementById('login-password').value
                };
                
                const response = await callApi(`http://webblog223.test/backend/auth/login`, 'POST', data);
                displayResponse(responseElem, response, response.success);
                
                // If login was successful, store token and reload
                if (response.success) {
                    await storeTokenInSession(response.data.token, response.data.user);
                }
            });

            // API Info
            document.getElementById('api-info-btn').addEventListener('click', async function() {
                const responseElem = document.getElementById('api-info-response');
                const response = await callApi(`http://webblog223.test/backend`, 'GET');
                displayResponse(responseElem, response, response.success);
            });

            // Get Current User
            document.getElementById('me-btn').addEventListener('click', async function() {
                const responseElem = document.getElementById('me-response');
                const response = await callApi(`http://webblog223.test/backend/auth/me`, 'GET', null, true);
                displayResponse(responseElem, response, response.success);
            });

            // Update Profile
            document.getElementById('update-profile-form').addEventListener('submit', async function(e) {
                e.preventDefault();
                const responseElem = document.getElementById('update-profile-response');
                
                const data = {};
                
                const username = document.getElementById('update-username').value;
                const email = document.getElementById('update-email').value;
                const currentPassword = document.getElementById('current-password').value;
                const newPassword = document.getElementById('new-password').value;
                
                if (username) data.username = username;
                if (email) data.email = email;
                if (currentPassword) data.currentPassword = currentPassword;
                if (newPassword) data.newPassword = newPassword;
                
                const response = await callApi(`http://webblog223.test/backend/auth/profile`, 'PUT', data, true);
                displayResponse(responseElem, response, response.success);
                
                // If update was successful and token was refreshed, store it
                if (response.success && response.data && response.data.token) {
                    await storeTokenInSession(response.data.token, response.data.user);
                }
            });

            // Delete Account
            document.getElementById('delete-account-form').addEventListener('submit', async function(e) {
                e.preventDefault();
                
                if (!confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
                    return;
                }
                
                const responseElem = document.getElementById('delete-account-response');
                
                const data = {
                    password: document.getElementById('delete-password').value
                };
                
                const response = await callApi(`http://webblog223.test/backend/auth/delete-account`, 'DELETE', data, true);
                displayResponse(responseElem, response, response.success);
                
                // If account was deleted successfully, logout and reload
                if (response.success) {
                    setTimeout(async function() {
                        window.location.href = `http://webblog223.test/frontend?logout=1`;
                    }, 2000);
                }
            });

            // Admin Only
            document.getElementById('admin-only-btn').addEventListener('click', async function() {
                const responseElem = document.getElementById('admin-only-response');
                const response = await callApi(`http://webblog223.test/backend/auth/admin-only`, 'GET', null, true);
                displayResponse(responseElem, response, response.success);
            });

            // Logout button
            if (document.getElementById('logout-btn')) {
                document.getElementById('logout-btn').addEventListener('click', function() {
                    window.location.href = `http://webblog223.test/frontend?logout=1`;
                });
            }
        });
    </script>
</body>
</html>
