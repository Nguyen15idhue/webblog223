<?php
require_once __DIR__ . '/../includes/database.php';

class Post {
    private $conn;
    private $table = 'posts';

    // Post Properties
    public $id;
    public $user_id;
    public $subject_id;
    public $title;
    public $content;
    public $status; // 0: hidden/pending, 1: active/approved
    public $created_at;
    public $updated_at;

    // Joined properties
    public $username;
    public $subject_name;
    public $likes_count;
    public $dislikes_count;

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    public function create($user_id, $subject_id, $title, $content, $status = 1) {
        try {
            $query = "INSERT INTO {$this->table} (user_id, subject_id, title, content, status) 
                      VALUES (:user_id, :subject_id, :title, :content, :status)";
            
            $stmt = $this->conn->prepare($query);

            $title = htmlspecialchars(strip_tags($title));
            $content = htmlspecialchars(strip_tags($content));

            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':subject_id', $subject_id);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':content', $content);
            $stmt->bindParam(':status', $status);

            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return $this->getById($this->id);
            }
            return false;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function getById($id) {
        try {
            $query = "SELECT p.*, u.username, s.subject_name,
                        (SELECT COUNT(*) FROM post_likes WHERE post_id = p.id AND like_status = 1) as likes_count,
                        (SELECT COUNT(*) FROM post_likes WHERE post_id = p.id AND like_status = -1) as dislikes_count
                      FROM {$this->table} p
                      LEFT JOIN users u ON p.user_id = u.id
                      LEFT JOIN subjects s ON p.subject_id = s.id
                      WHERE p.id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $this->id = $row['id'];
                $this->user_id = $row['user_id'];
                $this->subject_id = $row['subject_id'];
                $this->title = $row['title'];
                $this->content = $row['content'];
                $this->status = $row['status'];
                $this->created_at = $row['created_at'];
                $this->updated_at = $row['updated_at'];
                $this->username = $row['username'];
                $this->subject_name = $row['subject_name'];
                $this->likes_count = $row['likes_count'];
                $this->dislikes_count = $row['dislikes_count'];
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function getAll($orderBy = 'created_at', $order = 'DESC', $limit = 10, $offset = 0, $keyword = null, $userId = null, $subjectId = null, $onlyActive = true) {
        try {
            $query = "SELECT p.*, u.username, s.subject_name,
                        (SELECT COUNT(*) FROM post_likes WHERE post_id = p.id AND like_status = 1) as likes_count,
                        (SELECT COUNT(*) FROM post_likes WHERE post_id = p.id AND like_status = -1) as dislikes_count
                      FROM {$this->table} p
                      LEFT JOIN users u ON p.user_id = u.id
                      LEFT JOIN subjects s ON p.subject_id = s.id";
            
            $whereClauses = [];
            $params = [];

            if ($onlyActive) {
                $whereClauses[] = "p.status = 1";
            }
            if ($keyword) {
                $whereClauses[] = "(p.title LIKE :keyword OR p.content LIKE :keyword)";
                $params[':keyword'] = "%{$keyword}%";
            }
            if ($userId) {
                $whereClauses[] = "p.user_id = :user_id";
                $params[':user_id'] = $userId;
            }
            if ($subjectId) {
                $whereClauses[] = "p.subject_id = :subject_id";
                $params[':subject_id'] = $subjectId;
            }

            if (!empty($whereClauses)) {
                $query .= " WHERE " . implode(' AND ', $whereClauses);
            }

            $query .= " ORDER BY p.{$orderBy} {$order}";
            $query .= " LIMIT {$limit} OFFSET {$offset}";

            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function countAll($keyword = null, $userId = null, $subjectId = null, $onlyActive = true) {
        try {
            $query = "SELECT COUNT(*) as total FROM {$this->table}";
            $whereClauses = [];
            $params = [];

            if ($onlyActive) {
                $whereClauses[] = "status = 1";
            }
             if ($keyword) {
                $whereClauses[] = "(title LIKE :keyword OR content LIKE :keyword)";
                $params[':keyword'] = "%{$keyword}%";
            }
            if ($userId) {
                $whereClauses[] = "user_id = :user_id";
                $params[':user_id'] = $userId;
            }
            if ($subjectId) {
                $whereClauses[] = "subject_id = :subject_id";
                $params[':subject_id'] = $subjectId;
            }

            if (!empty($whereClauses)) {
                $query .= " WHERE " . implode(' AND ', $whereClauses);
            }

            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$row['total'];
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return 0;
        }
    }

    public function update($id, $data) {
        try {
            $updateFields = [];
            $params = [];

            if (isset($data['title'])) {
                $updateFields[] = 'title = :title';
                $params[':title'] = htmlspecialchars(strip_tags($data['title']));
            }
            if (isset($data['content'])) {
                $updateFields[] = 'content = :content';
                $params[':content'] = htmlspecialchars(strip_tags($data['content']));
            }
            if (isset($data['subject_id'])) {
                $updateFields[] = 'subject_id = :subject_id';
                $params[':subject_id'] = $data['subject_id'];
            }
            if (isset($data['status'])) {
                $updateFields[] = 'status = :status';
                $params[':status'] = (int)$data['status'];
            }

            if (empty($updateFields)) return false;

            $query = "UPDATE {$this->table} SET " . implode(', ', $updateFields) . " WHERE id = :id";
            $params[':id'] = $id;

            $stmt = $this->conn->prepare($query);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function delete($id) {
        try {
            // Also delete likes/dislikes associated with the post
            $likeQuery = "DELETE FROM post_likes WHERE post_id = :post_id";
            $likeStmt = $this->conn->prepare($likeQuery);
            $likeStmt->bindParam(':post_id', $id);
            $likeStmt->execute();

            $query = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function likeOrDislike($postId, $userId, $likeStatus) {
        try {
            // Check if a record already exists
            $query = "SELECT * FROM post_likes WHERE post_id = :post_id AND user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':post_id' => $postId, ':user_id' => $userId]);
            $existing = $stmt->fetch();

            if ($existing) {
                // If the new status is the same as the old one, it's a "remove" action
                if ($existing['like_status'] == $likeStatus) {
                    $updateQuery = "DELETE FROM post_likes WHERE id = :id";
                    $updateStmt = $this->conn->prepare($updateQuery);
                    return $updateStmt->execute([':id' => $existing['id']]);
                } else {
                    // Update existing record
                    $updateQuery = "UPDATE post_likes SET like_status = :like_status WHERE id = :id";
                    $updateStmt = $this->conn->prepare($updateQuery);
                    return $updateStmt->execute([':like_status' => $likeStatus, ':id' => $existing['id']]);
                }
            } else {
                // Insert new record
                $insertQuery = "INSERT INTO post_likes (post_id, user_id, like_status) VALUES (:post_id, :user_id, :like_status)";
                $insertStmt = $this->conn->prepare($insertQuery);
                return $insertStmt->execute([':post_id' => $postId, ':user_id' => $userId, ':like_status' => $likeStatus]);
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'subject_id' => $this->subject_id,
            'title' => $this->title,
            'content' => $this->content,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'username' => $this->username,
            'subject_name' => $this->subject_name,
            'likes_count' => (int)$this->likes_count,
            'dislikes_count' => (int)$this->dislikes_count,
        ];
    }
}
