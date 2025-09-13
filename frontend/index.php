<?php
// Define base URL for frontend dynamically
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$base_url = $protocol . $host . '/frontend';
$api_url = $protocol . $host . '/backend';

// Check if user is logged in by checking for token in session or cookie
session_start();
$isLoggedIn = isset($_SESSION['token']);
$userData = null;

if ($isLoggedIn) {
    // Get token from session
    $token = $_SESSION['token'];
    
    // Get user data
    $userData = isset($_SESSION['user']) ? $_SESSION['user'] : null;
}

// Handle logout
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    // Clear session
    session_unset();
    session_destroy();
    
    // Redirect to home page
    header('Location: ' . $base_url);
    exit;
}

// Get current page from URL
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebBlog223</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?php echo $base_url; ?>">WebBlog223</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page === 'home' ? 'active' : ''; ?>" href="<?php echo $base_url; ?>">Home</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <?php if ($isLoggedIn): ?>
                        <?php if (isset($userData) && $userData['role'] === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $page === 'admin' ? 'active' : ''; ?>" href="<?php echo $base_url; ?>?page=admin">Admin Panel</a>
                        </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $page === 'dashboard' ? 'active' : ''; ?>" href="<?php echo $base_url; ?>?page=dashboard">Dashboard</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo htmlspecialchars($userData['username']); ?>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="<?php echo $base_url; ?>?page=profile">My Profile</a></li>
                                <li><a class="dropdown-item" href="<?php echo $base_url; ?>?page=profile_management">Account Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo $base_url; ?>?logout=1">Logout</a></li>
                            </ul>
                        </li>

                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $page === 'login' ? 'active' : ''; ?>" href="<?php echo $base_url; ?>?page=login">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $page === 'register' ? 'active' : ''; ?>" href="<?php echo $base_url; ?>?page=register">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <?php
        // Load the appropriate page
        switch ($page) {
            case 'login':
                include 'pages/login.php';
                break;
            case 'register':
                include 'pages/register.php';
                break;
            case 'dashboard':
                if ($isLoggedIn) {
                    include 'pages/dashboard.php';
                } else {
                    include 'pages/login.php';
                }
                break;
            case 'admin':
                if ($isLoggedIn && $userData['role'] === 'admin') {
                    include 'pages/admin.php';
                } else {
                    include 'pages/unauthorized.php';
                }
                break;
            case 'profile':
                if ($isLoggedIn) {
                    include 'pages/profile.php';
                } else {
                    include 'pages/login.php';
                }
                break;
            case 'profile_management':
                if ($isLoggedIn) {
                    include 'pages/profile_management.php';
                } else {
                    include 'pages/login.php';
                }
                break;
            default:
                include 'pages/home.php';
        }
        ?>
    </div>

    <!-- Footer -->
    <footer class="bg-light text-center py-3 mt-5">
        <div class="container">
            <p>&copy; 2023 WebBlog223 - A simple PHP blog</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/app.js"></script>
</body>
</html>
