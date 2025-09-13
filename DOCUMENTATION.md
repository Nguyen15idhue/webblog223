# WebBlog223 - Giải Thích Mã Nguồn và API Routes

## Mục Lục
1. [Giới Thiệu Dự Án](#giới-thiệu-dự-án)
2. [Kiến Trúc Tổng Thể](#kiến-trúc-tổng-thể)
3. [Cấu Trúc Thư Mục](#cấu-trúc-thư-mục)
4. [Nguyên Lý Hoạt Động](#nguyên-lý-hoạt-động)
   - [Request Flow](#request-flow)
   - [Mô Hình MVC](#mô-hình-mvc)
   - [Authentication System](#authentication-system)
5. [API Routes](#api-routes)
   - [Auth Routes](#auth-routes)
   - [Cách Sử Dụng API](#cách-sử-dụng-api)
6. [Database Schema](#database-schema)
7. [Frontend Architecture](#frontend-architecture)
8. [Tính Năng Nâng Cao](#tính-năng-nâng-cao)

## Giới Thiệu Dự Án

WebBlog223 là một ứng dụng web blog phát triển bằng PHP thuần không sử dụng framework hay thư viện bên ngoài. Dự án này triển khai theo kiến trúc tách biệt rõ ràng giữa Backend và Frontend:

- **Backend**: Cung cấp các API JSON thuần túy
- **Frontend**: Hiển thị giao diện người dùng sử dụng PHP+HTML kết hợp JavaScript

Dự án áp dụng các nguyên tắc lập trình hướng đối tượng, bảo mật và khả năng mở rộng.

## Kiến Trúc Tổng Thể

WebBlog223 được xây dựng theo mô hình MVC (Model-View-Controller) được tùy chỉnh để phù hợp với kiến trúc API:

- **Model**: Quản lý dữ liệu và tương tác với database
- **Controller**: Xử lý các request, thực hiện nghiệp vụ và trả về response
- **View**: Không có trực tiếp trong backend, thay vào đó là JSON responses; giao diện người dùng được xây dựng riêng ở frontend

Các nguyên tắc thiết kế chính được áp dụng:
- Separation of Concerns (SoC)
- RESTful API design
- Stateless authentication với JWT (JSON Web Token)
- Security best practices (password hashing, input sanitization, etc.)

## Cấu Trúc Thư Mục

Dự án được tổ chức thành hai thư mục chính:

### Backend
```
/backend/
  /controllers/     # Xử lý các request và điều phối dữ liệu
  /models/          # Tương tác với database
  /services/        # Xử lý logic nghiệp vụ
  /includes/        # Các tiện ích và cấu hình
  /middleware/      # Xác thực và phân quyền
  /routes/          # Định tuyến request
  .htaccess         # Cấu hình Apache cho API
  index.php         # Entry point
```

### Frontend
```
/frontend/
  /css/             # Stylesheet files
  /js/              # JavaScript files
  /pages/           # Các trang giao diện
  index.php         # Entry point
  session-handler.php # Quản lý phiên người dùng
```

## Nguyên Lý Hoạt Động

### Request Flow

Khi một request được gửi đến server, quá trình xử lý diễn ra như sau:

1. Request đến `/backend/index.php`
2. Hệ thống xác định route từ URL
3. Route được chuyển đến controller tương ứng
4. Controller xử lý request (thông qua service nếu cần)
5. Service tương tác với model để truy xuất/cập nhật dữ liệu
6. Model thực hiện các thao tác với database
7. Kết quả trả về theo chuỗi ngược lại
8. Controller trả về JSON response

### Mô Hình MVC

#### Model
- Đại diện cho dữ liệu và logic nghiệp vụ
- Cung cấp các phương thức CRUD (Create, Read, Update, Delete)
- Tương tác trực tiếp với database qua PDO
- Ví dụ: `User` model quản lý thông tin người dùng

#### Controller
- Xử lý các request từ client
- Điều phối luồng dữ liệu giữa Model và response
- Trả về JSON response cho client
- Ví dụ: `AuthController` xử lý các hoạt động xác thực

#### Service
- Chứa logic nghiệp vụ phức tạp
- Kết nối controller với model
- Thực hiện xử lý dữ liệu và validation
- Ví dụ: `AuthService` xử lý đăng ký, đăng nhập, JWT

### Authentication System

Hệ thống xác thực dựa trên JWT (JSON Web Token):

1. Người dùng đăng nhập cung cấp username/email và password
2. Server xác thực thông tin đăng nhập
3. Nếu hợp lệ, server tạo JWT chứa thông tin người dùng
4. JWT được trả về cho client và lưu trữ
5. Các request tiếp theo đính kèm JWT trong header
6. Server xác thực JWT trước khi xử lý request
7. Middleware bảo vệ các route cần xác thực

JWT bao gồm 3 phần:
- Header: Thuật toán mã hóa
- Payload: Dữ liệu người dùng (ID, username, role, etc.)
- Signature: Chữ ký để đảm bảo tính toàn vẹn

## API Routes

WebBlog223 cung cấp các API endpoints sau:

### Auth Routes

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|--------------|
| POST | `/auth/register` | Đăng ký người dùng mới | No |
| POST | `/auth/login` | Đăng nhập và lấy token | No |
| GET | `/auth/me` | Lấy thông tin người dùng hiện tại | Yes |
| PUT/POST | `/auth/profile` | Cập nhật thông tin cá nhân | Yes |
| DELETE/POST | `/auth/delete-account` | Xóa tài khoản người dùng | Yes |
| GET | `/auth/admin-only` | Endpoint chỉ dành cho admin | Yes (Admin) |

### Cách Sử Dụng API

#### Đăng ký người dùng mới
```
POST /backend/auth/register
Content-Type: application/json

{
  "username": "example_user",
  "email": "user@example.com",
  "password": "securepassword",
  "confirmPassword": "securepassword"
}
```

#### Đăng nhập
```
POST /backend/auth/login
Content-Type: application/json

{
  "username": "example_user", // hoặc email
  "password": "securepassword"
}
```

Response:
```json
{
  "success": true,
  "message": "Login successful",
  "status": 200,
  "data": {
    "token": "eyJhbG...",
    "user": {
      "id": 1,
      "username": "example_user",
      "email": "user@example.com",
      "role": "user",
      "created_at": "2023-01-01 12:00:00",
      "updated_at": "2023-01-01 12:00:00"
    }
  }
}
```

#### Lấy thông tin người dùng hiện tại
```
GET /backend/auth/me
Authorization: Bearer eyJhbG...
```

#### Cập nhật thông tin cá nhân
```
PUT /backend/auth/profile
Authorization: Bearer eyJhbG...
Content-Type: application/json

{
  "username": "new_username",
  "email": "new_email@example.com",
  "currentPassword": "current_password",
  "newPassword": "new_password"
}
```

#### Xóa tài khoản người dùng
```
DELETE /backend/auth/delete-account
Authorization: Bearer eyJhbG...
Content-Type: application/json

{
  "password": "your_password"
}
```

## Database Schema

Database `webblog223` chứa các bảng sau:

### Bảng `users`
```sql
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('user', 'admin') DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## Frontend Architecture

Frontend được phát triển với PHP kết hợp HTML, CSS (Bootstrap) và JavaScript:

- **PHP Templates**: Hiển thị nội dung động
- **JavaScript**: Tương tác với Backend API
- **Session Management**: Quản lý phiên đăng nhập
- **Responsive Design**: Giao diện thích ứng với thiết bị

Các trang chính:
- Home: Trang chủ hiển thị nội dung blog
- Login/Register: Trang đăng nhập và đăng ký
- Dashboard: Trang tổng quan cho người dùng đã đăng nhập
- Profile: Xem và chỉnh sửa thông tin cá nhân
- Profile Management: Quản lý tài khoản (cập nhật thông tin, đổi mật khẩu, xóa tài khoản)
- Admin Panel: Quản lý dành cho admin

## Tính Năng Nâng Cao

1. **JWT Authentication**: Xác thực an toàn và stateless
2. **Role-based Access Control**: Phân quyền người dùng (user/admin)
3. **Password Hashing**: Bảo mật mật khẩu với bcrypt
4. **Input Sanitization**: Bảo vệ khỏi XSS và SQL Injection
5. **CORS Support**: Hỗ trợ Cross-Origin Resource Sharing
6. **RESTful API**: Thiết kế API theo chuẩn REST
7. **Complete CRUD Operations**: Đầy đủ các thao tác Create, Read, Update, Delete
8. **Clean Architecture**: Kiến trúc rõ ràng, dễ mở rộng
