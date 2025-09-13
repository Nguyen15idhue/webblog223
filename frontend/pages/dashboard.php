<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>User Dashboard</h4>
            </div>
            <div class="card-body">
                <h5>Welcome, <?php echo htmlspecialchars($userData['username']); ?>!</h5>
                <p>This is your user dashboard.</p>
                
                <div class="alert alert-info">
                    <h6>Your Account Information</h6>
                    <ul>
                        <li><strong>Username:</strong> <?php echo htmlspecialchars($userData['username']); ?></li>
                        <li><strong>Email:</strong> <?php echo htmlspecialchars($userData['email']); ?></li>
                        <li><strong>Role:</strong> <?php echo htmlspecialchars($userData['role']); ?></li>
                        <li><strong>Account Created:</strong> <?php echo htmlspecialchars($userData['created_at']); ?></li>
                    </ul>
                </div>
                
                <?php if ($userData['role'] === 'admin'): ?>
                <div class="alert alert-warning">
                    <h6>Admin Access</h6>
                    <p>You have admin privileges. You can access the <a href="<?php echo $base_url; ?>?page=admin">Admin Panel</a>.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check token validity
    const token = '<?php echo $_SESSION['token'] ?? ''; ?>';
    
    if (token) {
        fetch('<?php echo $api_url; ?>/auth/me', {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token
            }
        })
        .then(response => {
            if (!response.ok) {
                // Token invalid, redirect to login
                window.location.href = '<?php echo $base_url; ?>?page=login';
            }
            return response.json();
        })
        .then(data => {
            if (!data.success) {
                // Token invalid, redirect to login
                window.location.href = '<?php echo $base_url; ?>?page=login';
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    } else {
        // No token, redirect to login
        window.location.href = '<?php echo $base_url; ?>?page=login';
    }
});
</script>
