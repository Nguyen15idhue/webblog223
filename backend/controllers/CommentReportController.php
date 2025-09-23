<?php
// controllers/CommentReportController.php

class CommentReportController {
    private $service;

    public function __construct($service) {
        $this->service = $service;
    }

    // POST /comment-reports
    public function createReport($data) {
        // sanitize minimal
        $payload = [
            'comment_id' => isset($data['comment_id']) ? (int)$data['comment_id'] : null,
            'reporter_id' => isset($data['reporter_id']) ? (int)$data['reporter_id'] : null,
            'reason' => isset($data['reason']) ? sanitizeInput($data['reason']) : ''
        ];

        $res = $this->service->createReport($payload);
        if ($res) {
            jsonResponse(['success' => true, 'report' => $res], 201);
        } else {
            jsonResponse(['success' => false, 'message' => 'Failed to create report'], 400);
        }
    }

    // GET /comment-reports/{comment_id}
    public function getReportsByComment($comment_id) {
        $reports = $this->service->getReportsByComment((int)$comment_id);
        jsonResponse(['success' => true, 'data' => $reports]);
    }

    // GET /comment-reports  (admin)
    public function getAllReports() {
        $reports = $this->service->getAllReports();
        jsonResponse(['success' => true, 'data' => $reports]);
    }

    // DELETE /comment-reports/{id}
    public function deleteReport($id) {
        if ($this->service->deleteReport((int)$id)) {
            jsonResponse(['success' => true, 'message' => 'Report deleted']);
        } else {
            jsonResponse(['success' => false, 'message' => 'Failed to delete report'], 400);
        }
    }
}
