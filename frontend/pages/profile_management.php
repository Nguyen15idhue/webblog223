<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h3>Profile Management</h3>
            </div>
            <div class="card-body">
                <div id="profile-error" class="alert alert-danger d-none"></div>
                <div id="profile-success" class="alert alert-success d-none">Profile updated successfully!</div>
                
                <form id="profile-form">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo $userData['username'] ?? ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $userData['email'] ?? ''; ?>" required>
                    </div>
                    
                    <hr class="my-4">
                    <h5>Change Password</h5>
                    <p class="text-muted small">Leave blank if you don't want to change your password</p>
                    
                    <div class="mb-3">
                        <label for="currentPassword" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="currentPassword" name="currentPassword">
                    </div>
                    
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="newPassword" name="newPassword">
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirmNewPassword" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirmNewPassword" name="confirmNewPassword">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>
        
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h3>Danger Zone</h3>
            </div>
            <div class="card-body">
                <h5>Delete Account</h5>
                <p>Once you delete your account, there is no going back. Please be certain.</p>
                
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                    Delete Account
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteAccountModalLabel">Confirm Account Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="delete-error" class="alert alert-danger d-none"></div>
                <p>This action cannot be undone. All your data will be permanently deleted.</p>
                <form id="delete-account-form">
                    <div class="mb-3">
                        <label for="deletePassword" class="form-label">Enter your password to confirm</label>
                        <input type="password" class="form-control" id="deletePassword" name="password" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-btn">Delete My Account</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const profileForm = document.getElementById('profile-form');
    const deleteAccountForm = document.getElementById('delete-account-form');
    const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
    
    const profileErrorAlert = document.getElementById('profile-error');
    const profileSuccessAlert = document.getElementById('profile-success');
    const deleteErrorAlert = document.getElementById('delete-error');
    
    // Update Profile Form
    profileForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form values
        const username = document.getElementById('username').value.trim();
        const email = document.getElementById('email').value.trim();
        const currentPassword = document.getElementById('currentPassword').value;
        const newPassword = document.getElementById('newPassword').value;
        const confirmNewPassword = document.getElementById('confirmNewPassword').value;
        
        // Reset alerts
        profileErrorAlert.classList.add('d-none');
        profileSuccessAlert.classList.add('d-none');
        
        // Validate password match if new password is provided
        if (newPassword && newPassword !== confirmNewPassword) {
            profileErrorAlert.textContent = 'New passwords do not match';
            profileErrorAlert.classList.remove('d-none');
            return;
        }
        
        // Prepare data for API
        const updateData = {
            username: username,
            email: email
        };
        
        // Add password data if provided
        if (currentPassword && newPassword) {
            updateData.currentPassword = currentPassword;
            updateData.newPassword = newPassword;
        }
        
        // Call update profile API
        fetch('<?php echo $api_url; ?>/auth/profile', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer <?php echo $_SESSION["token"] ?? ""; ?>'
            },
            body: JSON.stringify(updateData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                profileSuccessAlert.classList.remove('d-none');
                
                // Update token if it was refreshed
                if (data.data && data.data.token) {
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
                        // Reload the page to update the displayed info
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    });
                } else {
                    // Reload the page to update the displayed info
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
                
                // Clear password fields
                document.getElementById('currentPassword').value = '';
                document.getElementById('newPassword').value = '';
                document.getElementById('confirmNewPassword').value = '';
            } else {
                // Show error message
                profileErrorAlert.textContent = data.message || 'Failed to update profile';
                profileErrorAlert.classList.remove('d-none');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            profileErrorAlert.textContent = 'An error occurred. Please try again later.';
            profileErrorAlert.classList.remove('d-none');
        });
    });
    
    // Delete Account Button
    confirmDeleteBtn.addEventListener('click', function() {
        const password = document.getElementById('deletePassword').value;
        
        // Reset alert
        deleteErrorAlert.classList.add('d-none');
        
        if (!password) {
            deleteErrorAlert.textContent = 'Please enter your password to confirm';
            deleteErrorAlert.classList.remove('d-none');
            return;
        }
        
        // Call delete account API
        fetch('<?php echo $api_url; ?>/auth/delete-account', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer <?php echo $_SESSION["token"] ?? ""; ?>'
            },
            body: JSON.stringify({
                password: password
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Account deleted successfully, logout and redirect
                fetch('<?php echo $base_url; ?>?logout=1').then(() => {
                    window.location.href = '<?php echo $base_url; ?>?page=login&deleted=1';
                });
            } else {
                // Show error message
                deleteErrorAlert.textContent = data.message || 'Failed to delete account';
                deleteErrorAlert.classList.remove('d-none');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            deleteErrorAlert.textContent = 'An error occurred. Please try again later.';
            deleteErrorAlert.classList.remove('d-none');
        });
    });
});
</script>
