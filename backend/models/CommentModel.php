<?php
class CommentModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Lấy tất cả bình luận đã duyệt theo post_id
    public function getApprovedByPost($post_id) {
        $sql = "SELECT c.id, c.post_id, c.user_id, u.username, c.content, c.status, c.created_at
                FROM comments c
                JOIN users u ON c.user_id = u.id
                WHERE c.post_id = :post_id AND c.status = 'approved'
                ORDER BY c.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    // Tạo bình luận mới
    public function create($data) {
        $sql = "INSERT INTO comments (post_id, user_id, content, status) 
                VALUES (:post_id, :user_id, :content, 'pending')";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':post_id', $data['post_id'], PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':content', $data['content'], PDO::PARAM_STR);

        if ($stmt->execute()) {
            // Trả về dữ liệu vừa insert để dễ test
            return [
                "id" => $this->db->lastInsertId(),
                "post_id" => $data['post_id'],
                "user_id" => $data['user_id'],
                "content" => $data['content'],
                "status" => "pending"
            ];
        }
        return false;
    }

    // Cập nhật bình luận
    public function update($id, $data) {
        $sql = "UPDATE comments SET content = :content WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':content', $data['content'], PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Xóa bình luận
    public function delete($id) {
        $sql = "DELETE FROM comments WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Duyệt bình luận
    public function approve($id) {
        $sql = "UPDATE comments SET status = 'approved' WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Từ chối bình luận
    public function reject($id) {
        $sql = "UPDATE comments SET status = 'rejected' WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
