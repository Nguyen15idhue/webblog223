# Cơ chế JWT trong WebBlog223

## Giới thiệu về JWT

JWT (JSON Web Token) là một tiêu chuẩn mở (RFC 7519) định nghĩa một cách nhỏ gọn và độc lập để truyền thông tin một cách an toàn giữa các bên dưới dạng đối tượng JSON. WebBlog223 sử dụng JWT để xây dựng hệ thống xác thực và phân quyền stateless.

## Cấu trúc JWT trong WebBlog223

Trong dự án WebBlog223, mỗi JWT bao gồm ba phần, được phân tách bằng dấu chấm (.):

1. **Header**: Chứa thông tin về loại token và thuật toán mã hóa
2. **Payload**: Chứa thông tin người dùng và metadata
3. **Signature**: Chữ ký để xác minh token không bị sửa đổi

## Triển khai JWT trong code

### 1. Cấu hình JWT

File `includes/config.php` định nghĩa các thông số JWT cơ bản:

```php
// JWT settings
define('JWT_SECRET', 'your_jwt_secret_key_here'); // Key bí mật
define('JWT_EXPIRATION', 3600); // Thời gian hiệu lực token (1 giờ)
```

### 2. Tạo JWT Token (Encode)

JWT được tạo trong function `generateJWT()` (file `includes/utils.php`):

```php
function generateJWT($payload) {
    // Tạo header với thuật toán HS256
    $header = json_encode([
        'typ' => 'JWT',
        'alg' => 'HS256'
    ]);
    
    // Thêm thông tin về thời gian hết hạn và thời gian tạo token
    $payload['exp'] = time() + JWT_EXPIRATION;
    $payload['iat'] = time();
    
    // Mã hóa header và payload dưới dạng base64url
    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($payload)));
    
    // Tạo chữ ký bằng thuật toán HMAC SHA256
    $signature = hash_hmac('sha256', $base64UrlHeader . '.' . $base64UrlPayload, JWT_SECRET, true);
    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
    
    // Ghép 3 phần lại để tạo thành token hoàn chỉnh
    return $base64UrlHeader . '.' . $base64UrlPayload . '.' . $base64UrlSignature;
}
```

### 3. Xác thực JWT Token (Decode)

JWT được xác thực trong function `verifyJWT()` (file `includes/utils.php`):

```php
function verifyJWT($token) {
    // Tách token thành 3 phần
    $tokenParts = explode('.', $token);
    if (count($tokenParts) !== 3) {
        return false;
    }
    
    list($base64UrlHeader, $base64UrlPayload, $base64UrlSignature) = $tokenParts;
    
    // Xác minh chữ ký
    $signature = base64_decode(str_replace(['-', '_'], ['+', '/'], $base64UrlSignature));
    $expectedSignature = hash_hmac('sha256', $base64UrlHeader . '.' . $base64UrlPayload, JWT_SECRET, true);
    if (!hash_equals($signature, $expectedSignature)) {
        return false;
    }
    
    // Giải mã payload
    $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $base64UrlPayload)), true);
    
    // Kiểm tra thời hạn token
    if (isset($payload['exp']) && $payload['exp'] < time()) {
        return false;
    }
    
    return $payload;
}
```

### 4. Trích xuất JWT từ HTTP Header

Token được gửi trong request header theo chuẩn Bearer:

```php
function getBearerToken() {
    $headers = getallheaders();
    
    if (isset($headers['Authorization'])) {
        if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
            return $matches[1];
        }
    }
    
    return null;
}
```

## Quy trình xác thực sử dụng JWT

### 1. Đăng nhập và nhận token

Khi người dùng đăng nhập thành công, hệ thống tạo JWT chứa thông tin người dùng:

```php
// Trong AuthService::login()
$token = generateJWT([
    'sub' => $this->user->id,          // Subject (ID người dùng)
    'username' => $this->user->username,
    'email' => $this->user->email,
    'role' => $this->user->role        // Phân quyền (user/admin)
]);

// Trả về token cho client
return [
    'success' => true,
    'message' => 'Login successful',
    'status' => 200,
    'data' => [
        'token' => $token,
        'user' => $this->user->toArray()
    ]
];
```

### 2. Sử dụng token để xác thực

Cho mỗi request đến API được bảo vệ, client gửi token trong header:
```
Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
```

### 3. Middleware xác thực

API routes được bảo vệ bởi middleware xác thực:

```php
// Trong file middleware/authMiddleware.php
public static function authenticate($next) {
    $userData = self::isAuthenticated();
    
    if (!$userData) {
        jsonResponse([
            'success' => false,
            'message' => 'Unauthorized access'
        ], 401);
    }
    
    // Cho phép tiếp tục xử lý nếu xác thực thành công
    $next($userData);
}

public static function isAuthenticated() {
    // Lấy token từ header
    $token = getBearerToken();
    
    if (!$token) {
        return false;
    }
    
    // Xác thực token
    $payload = verifyJWT($token);
    
    if (!$payload) {
        return false;
    }
    
    // Xác minh người dùng tồn tại trong database
    $user = new User();
    if (!$user->getById($payload['sub'])) {
        return false;
    }
    
    return $user->toArray();
}
```

### 4. Middleware phân quyền Admin

Một số endpoints yêu cầu quyền admin được bảo vệ thêm một lớp:

```php
// Trong file middleware/authMiddleware.php
public static function adminGuard($next) {
    $userData = self::isAuthenticated();
    
    if (!$userData) {
        jsonResponse([
            'success' => false,
            'message' => 'Unauthorized access'
        ], 401);
    }
    
    if ($userData['role'] !== 'admin') {
        jsonResponse([
            'success' => false,
            'message' => 'Access denied. Admin role required.'
        ], 403);
    }
    
    // Cho phép tiếp tục nếu là admin
    $next($userData);
}
```

### 5. Áp dụng middleware vào routes

Routes API được bảo vệ bằng middleware tương ứng:

```php
// Trong file routes/auth.php
case '/auth/me':
    // Middleware to check if user is authenticated
    AuthMiddleware::authenticate(function($user) use ($authController) {
        $authController->getCurrentUser();
    });
    break;
    
case '/auth/admin-only':
    // Middleware to check if user is admin
    AuthMiddleware::adminGuard(function($user) use ($authController) {
        $authController->adminOnly();
    });
    break;
```

## Những tính năng bảo mật của JWT trong WebBlog223

1. **Thuật toán mã hóa an toàn**: Sử dụng HMAC với SHA-256 để tạo chữ ký
2. **Thời hạn token**: Mỗi token có thời gian sống giới hạn (1 giờ)
3. **Payload thông tin phong phú**: Lưu trữ ID, username, email và role
4. **Stateless**: Không lưu trữ trạng thái trên server
5. **Multi-factor verification**: Xác minh chữ ký và xác minh người dùng tồn tại trong database
6. **Role-based Access Control**: Phân quyền dựa trên thông tin trong token

## Quy trình làm mới token

Khi thông tin người dùng thay đổi (như username hoặc email), token được làm mới:

```php
// Trong AuthService::updateProfile()
// Nếu username hoặc email thay đổi, tạo token mới
if (isset($updateData['username']) || isset($updateData['email'])) {
    $token = generateJWT([
        'sub' => $this->user->id,
        'username' => $this->user->username,
        'email' => $this->user->email,
        'role' => $this->user->role
    ]);
    
    // Thêm token mới vào response
    $response['data']['token'] = $token;
}
```

## Kết luận

WebBlog223 sử dụng JWT để xây dựng hệ thống xác thực stateless, an toàn và linh hoạt. Việc triển khai JWT từ đầu (không dùng thư viện) giúp hiểu rõ cách thức hoạt động và có thể tùy chỉnh theo nhu cầu cụ thể của ứng dụng.
