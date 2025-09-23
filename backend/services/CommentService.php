<?php
class CommentService {
    private $model;

    public function __construct($model) {
        $this->model = $model;
    }

    public function getComments($post_id) {
        $stmt = $this->model->getApprovedByPost($post_id);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addComment($data) {
        return $this->model->create($data);
    }

    public function updateComment($id, $data) {
        return $this->model->update($id, $data);
    }

    public function deleteComment($id) {
        return $this->model->delete($id);
    }

    public function approveComment($id) {
        return $this->model->approve($id);
    }

    public function rejectComment($id) {
        return $this->model->reject($id);
    }
}
