<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Login</div>
            <div class="card-body">
                <div id="login-error" class="alert alert-danger d-none"></div>
                <form id="login-form">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username or Email</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Login</button>
                </form>
                <hr>
                <p>Don't have an account? <a href="<?php echo $base_url; ?>?page=register">Register now</a></p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('login-form');
    const errorAlert = document.getElementById('login-error');
    
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        
        // Reset error message
        errorAlert.classList.add('d-none');
        errorAlert.textContent = '';
        
        // Call login API
        fetch('<?php echo $api_url; ?>/auth/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                username: username,
                password: password
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Store token and user data in session storage
                <?php if (session_status() === PHP_SESSION_ACTIVE): ?>
                // Store in PHP session via AJAX
                fetch('<?php echo $base_url; ?>/session-handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        token: data.data.token,
                        user: data.data.user
                    })
                }).then(() => {
                    // Redirect to dashboard
                    window.location.href = '<?php echo $base_url; ?>?page=dashboard';
                });
                <?php else: ?>
                // Store in session storage (client-side)
                sessionStorage.setItem('token', data.data.token);
                sessionStorage.setItem('user', JSON.stringify(data.data.user));
                
                // Redirect to dashboard
                window.location.href = '<?php echo $base_url; ?>?page=dashboard';
                <?php endif; ?>
            } else {
                // Show error message
                errorAlert.textContent = data.message;
                errorAlert.classList.remove('d-none');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            errorAlert.textContent = 'An error occurred. Please try again later.';
            errorAlert.classList.remove('d-none');
        });
    });
});
</script>
