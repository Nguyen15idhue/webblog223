<div class="jumbotron">
    <h1 class="display-4">Welcome to WebBlog223</h1>
    <p class="lead">A simple PHP blog with separate backend API and frontend.</p>
    <hr class="my-4">
    <p>This is a demo project showcasing PHP development without frameworks.</p>
    <?php if (!$isLoggedIn): ?>
    <p>Please login or register to access the full features.</p>
    <a class="btn btn-primary btn-lg" href="<?php echo $base_url; ?>?page=register" role="button">Register Now</a>
    <?php else: ?>
    <a class="btn btn-primary btn-lg" href="<?php echo $base_url; ?>?page=dashboard" role="button">Go to Dashboard</a>
    <?php endif; ?>
</div>

<div class="row mt-5">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">PHP Backend API</h5>
                <p class="card-text">Pure PHP backend API that returns JSON responses. No frameworks used.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">PHP Frontend</h5>
                <p class="card-text">PHP frontend that renders HTML and makes API calls to fetch data.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">User Authentication</h5>
                <p class="card-text">Complete authentication system with register, login, and role-based access control.</p>
            </div>
        </div>
    </div>
</div>
