<?php
class CommentController {
    private $service;

    public function __construct($service) {
        $this->service = $service;
    }

    public function getComments($post_id) {
        jsonResponse($this->service->getComments($post_id));
    }

    public function addComment($data) {
    if ($this->service->addComment($data)) {
        // Trả lại nguyên dữ liệu mà client gửi
        jsonResponse($data);
    } else {
        jsonResponse(["success" => false, "message" => "Failed to submit comment"]);
    }
}

    public function updateComment($id, $data) {
        if ($this->service->updateComment($id, $data)) {
            jsonResponse(["success" => true, "message" => "Comment updated"]);
        } else {
            jsonResponse(["success" => false, "message" => "Failed to update comment"]);
        }
    }

    public function deleteComment($id) {
        if ($this->service->deleteComment($id)) {
            jsonResponse(["success" => true, "message" => "Comment deleted"]);
        } else {
            jsonResponse(["success" => false, "message" => "Failed to delete comment"]);
        }
    }

    public function approveComment($id) {
        if ($this->service->approveComment($id)) {
            jsonResponse(["success" => true, "message" => "Comment approved"]);
        } else {
            jsonResponse(["success" => false, "message" => "Failed to approve comment"]);
        }
    }

    public function rejectComment($id) {
        if ($this->service->rejectComment($id)) {
            jsonResponse(["success" => true, "message" => "Comment rejected"]);
        } else {
            jsonResponse(["success" => false, "message" => "Failed to reject comment"]);
        }
    }
}
