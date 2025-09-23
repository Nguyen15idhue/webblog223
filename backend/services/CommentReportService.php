<?php
// services/CommentReportService.php

class CommentReportService {
    private $reportModel;
    private $commentModel;
    private $autoRejectThreshold;

    /**
     * $commentModel is instance of CommentModel (to allow auto-reject)
     * $autoRejectThreshold: integer (default 5)
     */
    public function __construct($reportModel, $commentModel = null, $autoRejectThreshold = 5) {
        $this->reportModel = $reportModel;
        $this->commentModel = $commentModel;
        $this->autoRejectThreshold = (int)$autoRejectThreshold;
    }

    public function createReport($data) {
        // Basic validation
        if (empty($data['comment_id']) || empty($data['reporter_id'])) {
            return false;
        }

        $res = $this->reportModel->create($data);
        if ($res && $this->commentModel) {
            $count = $this->reportModel->countByComment($data['comment_id']);
            if ($count >= $this->autoRejectThreshold) {
                // auto reject the comment
                $this->commentModel->reject($data['comment_id']);
            }
        }
        return $res;
    }

    public function getReportsByComment($comment_id) {
        if (empty($comment_id)) return [];
        return $this->reportModel->getByComment($comment_id);
    }

    public function getAllReports() {
        return $this->reportModel->getAll();
    }

    public function deleteReport($id) {
        if (empty($id)) return false;
        return $this->reportModel->delete($id);
    }

    public function countReports($comment_id) {
        if (empty($comment_id)) return 0;
        return $this->reportModel->countByComment($comment_id);
    }
}
