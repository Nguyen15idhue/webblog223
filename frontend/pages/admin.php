<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h4>Admin Panel</h4>
            </div>
            <div class="card-body">
                <h5>Welcome to the Admin Panel</h5>
                <div id="admin-content" class="mt-3">
                    <div class="alert alert-info">Loading admin content...</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const adminContent = document.getElementById('admin-content');
    // Check admin access
    const token = '<?php echo $_SESSION['token'] ?? ''; ?>';
    
    if (token) {
        fetch('<?php echo $api_url; ?>/auth/admin-only', {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Display admin content
                adminContent.innerHTML = `
                    <div class="alert alert-success">
                        <h6>Admin Access Granted</h6>
                        <p>${data.message}</p>
                        <p>Timestamp: ${new Date(data.data.timestamp * 1000).toLocaleString()}</p>
                    </div>
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5>Admin Functions</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                <li class="list-group-item">User Management (Demo)</li>
                                <li class="list-group-item">Content Management (Demo)</li>
                                <li class="list-group-item">System Settings (Demo)</li>
                            </ul>
                        </div>
                    </div>
                `;
            } else {
                // Display error message
                adminContent.innerHTML = `
                    <div class="alert alert-danger">
                        <h6>Access Denied</h6>
                        <p>${data.message}</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            adminContent.innerHTML = `
                <div class="alert alert-danger">
                    <h6>Error</h6>
                    <p>An error occurred while fetching admin content.</p>
                </div>
            `;
        });
    } else {
        // No token, display error message
        adminContent.innerHTML = `
            <div class="alert alert-danger">
                <h6>Authentication Error</h6>
                <p>You are not logged in.</p>
            </div>
        `;
    }
});
</script>
