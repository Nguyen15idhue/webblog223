<?php
require_once __DIR__ . '/../includes/database.php';

class Subject {
    private $conn;
    private $table = 'subjects';
    
    // Subject properties
    public $id;
    public $subject_name;
    public $content_subject;
    public $status;
    public $created_at;
    public $updated_at;
    
    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }
    
    /**
     * Create new subject
     * 
     * @param string $subject_name
     * @param string $content_subject
     * @param int $status (1: Active, 0: Inactive)
     * @return boolean
     */
    public function create($subject_name, $content_subject, $status = 1) {
        try {
            $query = "INSERT INTO {$this->table} (subject_name, content_subject, status) 
                      VALUES (:subject_name, :content_subject, :status)";
            
            $stmt = $this->conn->prepare($query);
            
            // Clean data
            $subject_name = htmlspecialchars(strip_tags($subject_name));
            $content_subject = htmlspecialchars(strip_tags($content_subject));
            
            // Bind parameters
            $stmt->bindParam(':subject_name', $subject_name);
            $stmt->bindParam(':content_subject', $content_subject);
            $stmt->bindParam(':status', $status);
            
            // Execute query
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                
                // Get the newly created subject
                $this->getById($this->id);
                return true;
            }
            
            $error = $stmt->errorInfo();
            file_put_contents('debug_subject.log', 'Failed to execute query. Error: ' . implode(', ', $error) . PHP_EOL, FILE_APPEND);
            return false;
        } catch (PDOException $e) {
            file_put_contents('debug_subject.log', 'Exception creating subject: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
            return false;
        }
    }
    
    /**
     * Get subject by ID
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
                $this->subject_name = $row['subject_name'];
                $this->content_subject = $row['content_subject'];
                $this->status = $row['status'];
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
     * Get subject by name
     * 
     * @param string $subject_name
     * @return boolean
     */
    public function getByName($subject_name) {
        try {
            $query = "SELECT * FROM {$this->table} WHERE subject_name = :subject_name";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':subject_name', $subject_name);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                $this->id = $row['id'];
                $this->subject_name = $row['subject_name'];
                $this->content_subject = $row['content_subject'];
                $this->status = $row['status'];
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
     * Check if subject name already exists
     * 
     * @param string $subject_name
     * @return boolean
     */
    public function nameExists($subject_name) {
        try {
            $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE subject_name = :subject_name";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':subject_name', $subject_name);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $row['count'] > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Get all subjects
     * 
     * @param string $orderBy Field to order by (default: created_at)
     * @param string $order Order direction (ASC/DESC)
     * @param int $limit Number of records to return (0 = all)
     * @param int $offset Starting position of records
     * @return array
     */
    public function getAll($orderBy = 'created_at', $order = 'DESC', $limit = 0, $offset = 0) {
        try {
            $query = "SELECT * FROM {$this->table}";
            
            // Add order clause
            $query .= " ORDER BY {$orderBy} {$order}";
            
            // Add limit clause if specified
            if ($limit > 0) {
                $query .= " LIMIT {$limit}";
                
                if ($offset > 0) {
                    $query .= " OFFSET {$offset}";
                }
            }
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Get active subjects
     * 
     * @param string $orderBy Field to order by
     * @param string $order Order direction (ASC/DESC)
     * @param int $limit Number of records to return (0 = all)
     * @param int $offset Starting position of records
     * @return array
     */
    public function getActive($orderBy = 'created_at', $order = 'DESC', $limit = 0, $offset = 0) {
        try {
            $query = "SELECT * FROM {$this->table} WHERE status = 1";
            
            // Add order clause
            $query .= " ORDER BY {$orderBy} {$order}";
            
            // Add limit clause if specified
            if ($limit > 0) {
                $query .= " LIMIT {$limit}";
                
                if ($offset > 0) {
                    $query .= " OFFSET {$offset}";
                }
            }
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Count total subjects
     * 
     * @param boolean $activeOnly Count only active subjects
     * @return int
     */
    public function countAll($activeOnly = false) {
        try {
            $query = "SELECT COUNT(*) as total FROM {$this->table}";
            
            if ($activeOnly) {
                $query .= " WHERE status = 1";
            }
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return (int)$row['total'];
        } catch (PDOException $e) {
            return 0;
        }
    }
    
    /**
     * Search subjects
     * 
     * @param string $keyword Search keyword
     * @param int $limit Number of records to return (0 = all)
     * @param int $offset Starting position of records
     * @return array
     */
    public function search($keyword, $limit = 0, $offset = 0) {
        try {
            $searchTerm = "%{$keyword}%";
            
            $query = "SELECT * FROM {$this->table} 
                      WHERE subject_name LIKE :keyword 
                      OR content_subject LIKE :keyword";
            
            // Add limit clause if specified
            if ($limit > 0) {
                $query .= " LIMIT {$limit}";
                
                if ($offset > 0) {
                    $query .= " OFFSET {$offset}";
                }
            }
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':keyword', $searchTerm);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Get subject data
     * 
     * @return array Subject data
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'subject_name' => $this->subject_name,
            'content_subject' => $this->content_subject,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
    
    /**
     * Update subject
     * 
     * @param int $id Subject ID
     * @param array $data Data to update (keys: subject_name, content_subject, status)
     * @return boolean
     */
    public function update($id, $data) {
        try {
            // Build query based on provided data
            $updateFields = [];
            $params = [];
            
            if (isset($data['subject_name'])) {
                $updateFields[] = 'subject_name = :subject_name';
                $params[':subject_name'] = htmlspecialchars(strip_tags($data['subject_name']));
            }
            
            if (isset($data['content_subject'])) {
                $updateFields[] = 'content_subject = :content_subject';
                $params[':content_subject'] = htmlspecialchars(strip_tags($data['content_subject']));
            }
            
            if (isset($data['status'])) {
                $updateFields[] = 'status = :status';
                $params[':status'] = (int)$data['status'];
            }
            
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
                // Get updated subject data
                return $this->getById($id);
            }
            
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Delete subject
     * 
     * @param int $id Subject ID
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
    
    /**
     * Toggle subject status
     * 
     * @param int $id Subject ID
     * @return boolean
     */
    public function toggleStatus($id) {
        try {
            // First, get the current status
            if (!$this->getById($id)) {
                return false;
            }
            
            // Toggle the status
            $newStatus = $this->status ? 0 : 1;
            
            $query = "UPDATE {$this->table} SET status = :status WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':status', $newStatus);
            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute()) {
                $this->status = $newStatus;
                return true;
            }
            
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }
}