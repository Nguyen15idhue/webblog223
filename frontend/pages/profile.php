<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>User Profile</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center mb-4">
                            <img src="https://via.placeholder.com/150" class="rounded-circle" alt="Profile Image">
                            <h5 class="mt-3"><?php echo htmlspecialchars($userData['username']); ?></h5>
                            <p class="text-muted"><?php echo htmlspecialchars($userData['role']); ?></p>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h5>Account Information</h5>
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th>Username:</th>
                                    <td><?php echo htmlspecialchars($userData['username']); ?></td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td><?php echo htmlspecialchars($userData['email']); ?></td>
                                </tr>
                                <tr>
                                    <th>Role:</th>
                                    <td><?php echo htmlspecialchars($userData['role']); ?></td>
                                </tr>
                                <tr>
                                    <th>Account Created:</th>
                                    <td><?php echo htmlspecialchars($userData['created_at']); ?></td>
                                </tr>
                                <tr>
                                    <th>Last Updated:</th>
                                    <td><?php echo htmlspecialchars($userData['updated_at']); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
