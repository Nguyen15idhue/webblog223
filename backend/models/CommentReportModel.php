<?php
// models/CommentReportModel.php

class CommentReportModel {
    private $db;
    private $table = 'comment_reports';

    public function __construct($db) {
        $this->db = $db;
    }

    // Tạo 1 report
    public function create($data) {
        $sql = "INSERT INTO {$this->table} (comment_id, reporter_id, reason) VALUES (:comment_id, :reporter_id, :reason)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':comment_id', $data['comment_id'], PDO::PARAM_INT);
        $stmt->bindParam(':reporter_id', $data['reporter_id'], PDO::PARAM_INT);
        $stmt->bindParam(':reason', $data['reason'], PDO::PARAM_STR);
        if ($stmt->execute()) {
            return [
                'id' => $this->db->lastInsertId(),
                'comment_id' => $data['comment_id'],
                'reporter_id' => $data['reporter_id'],
                'reason' => $data['reason'],
                'created_at' => date('Y-m-d H:i:s')
            ];
        }
        return false;
    }

    // Lấy tất cả report của 1 comment
    public function getByComment($comment_id) {
        $sql = "SELECT * FROM {$this->table} WHERE comment_id = :comment_id ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':comment_id', $comment_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy tất cả report (admin)
    public function getAll() {
        $sql = "SELECT cr.*, c.content AS comment_content, u.username AS reporter_username
                FROM {$this->table} cr
                LEFT JOIN comments c ON cr.comment_id = c.id
                LEFT JOIN users u ON cr.reporter_id = u.id
                ORDER BY cr.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Xoá report theo id
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Đếm report cho 1 comment
    public function countByComment($comment_id) {
        $sql = "SELECT COUNT(*) as cnt FROM {$this->table} WHERE comment_id = :comment_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':comment_id', $comment_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) ($row['cnt'] ?? 0);
    }
}
