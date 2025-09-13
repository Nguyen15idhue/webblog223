# WebBlog223

A simple PHP blog with separate backend API and frontend.

## Project Structure

This project is built using pure PHP without any frameworks or external libraries. It separates the backend API (which only returns JSON) and the frontend (which renders HTML and makes API calls to the backend).

### Directory Structure

```
webblog223/
├── backend/
│   ├── controllers/  # Handle requests and responses
│   ├── includes/     # Database connection and utilities
│   ├── middleware/   # Request processing middleware
│   ├── models/       # Data models
│   ├── routes/       # API route definitions
│   ├── services/     # Business logic
│   ├── .htaccess     # URL rewriting for API
│   └── index.php     # Backend entry point
├── frontend/
│   ├── css/          # Stylesheets
│   ├── js/           # JavaScript files
│   ├── pages/        # Page templates
│   ├── index.php     # Frontend entry point
│   └── session-handler.php  # Session management
└── db_setup.sql      # Database setup script
```

## Features

- Separate backend API and frontend
- Complete authentication system
  - Registration
  - Login
  - Logout
  - Role-based access control
- JWT authentication
- PDO for database access
- Clean separation of concerns

## Setup Instructions

1. Clone the repository or copy the files to your web server directory.
2. Create a MySQL database and import the `db_setup.sql` file.
3. Configure database connection in `backend/includes/database.php`.
4. Update base URLs in `backend/includes/config.php` and `frontend/index.php` if needed.
5. Access the application through your web server (e.g., http://localhost/webblog223/frontend).

## Default Users

- Admin: username `admin`, password `admin123`
- User: username `user`, password `user123`

## API Endpoints

- `POST /auth/register` - Register a new user
- `POST /auth/login` - Login user
- `GET /auth/me` - Get current user information
- `GET /auth/admin-only` - Admin only endpoint

## Request Flow

1. Frontend makes a request to the backend API using fetch/Ajax
2. Backend API processes the request through:
   - Route handling (determines which controller to use)
   - Controller (processes the request and calls services)
   - Service (contains business logic)
   - Model (interacts with the database)
3. Backend API returns a JSON response
4. Frontend processes the response and updates the UI accordingly

## Technologies Used

- PHP (Backend and Frontend)
- MySQL (Database)
- JavaScript (Frontend interaction)
- HTML/CSS (Frontend presentation)
- Bootstrap (Frontend styling)
- JWT (Authentication)

## Security Features

- Password hashing
- JWT token-based authentication
- PDO prepared statements to prevent SQL injection
- Input sanitization
- CORS protection
- Role-based access control
