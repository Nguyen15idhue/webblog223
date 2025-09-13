<?php
require_once __DIR__ . '/../includes/database.php';

class User {
    private $conn;
    private $table = 'users';
    
    // User properties
    public $id;
    public $username;
    public $email;
    public $password;
    public $role;
    public $created_at;
    public $updated_at;
    
    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }
    
    /**
     * Create new user
     * 
     * @param string $username
     * @param string $email
     * @param string $password
     * @param string $role
     * @return boolean
     */
    public function create($username, $email, $password, $role = 'user') {
        try {
            $query = "INSERT INTO {$this->table} (username, email, password, role) VALUES (:username, :email, :password, :role)";
            
            $stmt = $this->conn->prepare($query);
            
            // Hash password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Bind parameters
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password_hash);
            $stmt->bindParam(':role', $role);
            
            // Execute query
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                
                // Get the newly created user
                $this->getById($this->id);
                return true;
            }
            
            $error = $stmt->errorInfo();
            file_put_contents('debug_user.log', 'Failed to execute query. Error: ' . implode(', ', $error) . PHP_EOL, FILE_APPEND);
            return false;
        } catch (PDOException $e) {
            file_put_contents('debug_user.log', 'Exception creating user: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
            return false;
        }
    }
    
    /**
     * Get user by ID
     * 
     * @param int $id
     * @return boolean
     */
    public function getById($id) {
        try {
            $query = "SELECT * FROM {$this->table} WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                $this->id = $row['id'];
                $this->username = $row['username'];
                $this->email = $row['email'];
                $this->password = $row['password'];
                $this->role = $row['role'];
                $this->created_at = $row['created_at'];
                $this->updated_at = $row['updated_at'];
                return true;
            }
            
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Get user by username
     * 
     * @param string $username
     * @return boolean
     */
    public function getByUsername($username) {
        try {
            $query = "SELECT * FROM {$this->table} WHERE username = :username";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                $this->id = $row['id'];
                $this->username = $row['username'];
                $this->email = $row['email'];
                $this->password = $row['password'];
                $this->role = $row['role'];
                $this->created_at = $row['created_at'];
                $this->updated_at = $row['updated_at'];
                return true;
            }
            
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Get user by email
     * 
     * @param string $email
     * @return boolean
     */
    public function getByEmail($email) {
        try {
            $query = "SELECT * FROM {$this->table} WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                $this->id = $row['id'];
                $this->username = $row['username'];
                $this->email = $row['email'];
                $this->password = $row['password'];
                $this->role = $row['role'];
                $this->created_at = $row['created_at'];
                $this->updated_at = $row['updated_at'];
                return true;
            }
            
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Check if username already exists
     * 
     * @param string $username
     * @return boolean
     */
    public function usernameExists($username) {
        try {
            $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE username = :username";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $row['count'] > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Check if email already exists
     * 
     * @param string $email
     * @return boolean
     */
    public function emailExists($email) {
        try {
            $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $row['count'] > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Verify password
     * 
     * @param string $password
     * @return boolean
     */
    public function verifyPassword($password) {
        return password_verify($password, $this->password);
    }
    
    /**
     * Get user data without password
     * 
     * @return array User data
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
    
    /**
     * Update user
     * 
     * @param int $id User ID
     * @param array $data Data to update (keys: username, email, password, role)
     * @return boolean
     */
    public function update($id, $data) {
        try {
            // Build query based on provided data
            $updateFields = [];
            $params = [];
            
            if (isset($data['username'])) {
                $updateFields[] = 'username = :username';
                $params[':username'] = $data['username'];
            }
            
            if (isset($data['email'])) {
                $updateFields[] = 'email = :email';
                $params[':email'] = $data['email'];
            }
            
            if (isset($data['password'])) {
                $updateFields[] = 'password = :password';
                $params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            
            if (isset($data['role'])) {
                $updateFields[] = 'role = :role';
                $params[':role'] = $data['role'];
            }
            
            // Add updated_at timestamp
            $updateFields[] = 'updated_at = NOW()';
            
            // If no fields to update
            if (empty($updateFields)) {
                return false;
            }
            
            // Build the SQL query
            $query = "UPDATE {$this->table} SET " . implode(', ', $updateFields) . " WHERE id = :id";
            $params[':id'] = $id;
            
            // Prepare and execute
            $stmt = $this->conn->prepare($query);
            
            if ($stmt->execute($params)) {
                // Get updated user data
                return $this->getById($id);
            }
            
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Delete user
     * 
     * @param int $id User ID
     * @return boolean
     */
    public function delete($id) {
        try {
            $query = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}
