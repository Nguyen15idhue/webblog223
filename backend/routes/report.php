<?php
// routes/report.php

require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../models/CommentReportModel.php';
require_once __DIR__ . '/../models/CommentModel.php'; // for auto-reject
require_once __DIR__ . '/../services/CommentReportService.php';
require_once __DIR__ . '/../controllers/CommentReportController.php';
require_once __DIR__ . '/../includes/utils.php';

function reportRoutes($route) {
    $db = Database::getInstance()->getConnection();
    $reportModel = new CommentReportModel($db);
    $commentModel = new CommentModel($db);
    // threshold 5 (you can change)
    $service = new CommentReportService($reportModel, $commentModel, 5);
    $controller = new CommentReportController($service);

    $request_method = $_SERVER['REQUEST_METHOD'];
    $parts = explode('/', trim($route, '/')); // e.g. ['comment-reports', '1']

    if ($parts[0] === 'comment-reports') {
        switch ($request_method) {
            case 'POST':
                $data = getJsonBody();
                $controller->createReport($data);
                break;

            case 'GET':
                // GET /comment-reports -> all reports (admin)
                // GET /comment-reports/{comment_id} -> reports for comment
                if (isset($parts[1]) && is_numeric($parts[1])) {
                    $controller->getReportsByComment($parts[1]);
                } else {
                    $controller->getAllReports();
                }
                break;

            case 'DELETE':
                if (isset($parts[1]) && is_numeric($parts[1])) {
                    $controller->deleteReport($parts[1]);
                } else {
                    jsonResponse(['success' => false, 'message' => 'Report ID required'], 400);
                }
                break;

            default:
                jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
        }
    }
}
