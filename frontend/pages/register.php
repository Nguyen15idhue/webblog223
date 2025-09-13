<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Register</div>
            <div class="card-body">
                <div id="register-error" class="alert alert-danger d-none"></div>
                <div id="register-success" class="alert alert-success d-none">
                    Registration successful! You can now <a href="<?php echo $base_url; ?>?page=login">login</a>.
                </div>
                <form id="register-form">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Register</button>
                </form>
                <hr>
                <p>Already have an account? <a href="<?php echo $base_url; ?>?page=login">Login</a></p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('register-form');
    const errorAlert = document.getElementById('register-error');
    const successAlert = document.getElementById('register-success');
    
    registerForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const username = document.getElementById('username').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirmPassword').value;
        
        // Reset alerts
        errorAlert.classList.add('d-none');
        errorAlert.textContent = '';
        successAlert.classList.add('d-none');
        
        // Validate password match
        if (password !== confirmPassword) {
            errorAlert.textContent = 'Passwords do not match';
            errorAlert.classList.remove('d-none');
            return;
        }
        
        // Call register API
        fetch('<?php echo $api_url; ?>/auth/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                username: username,
                email: email,
                password: password,
                confirmPassword: confirmPassword
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                successAlert.classList.remove('d-none');
                registerForm.reset();
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
