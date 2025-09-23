<?php
require_once __DIR__ . '/../controllers/CommentController.php';
require_once __DIR__ . '/../services/CommentService.php';
require_once __DIR__ . '/../models/CommentModel.php';
require_once __DIR__ . '/../includes/database.php'; 

function commentRoutes($route) {
    $db = Database::getInstance()->getConnection();
    $model = new CommentModel($db);
    $service = new CommentService($model);
    $controller = new CommentController($service);

    $request_method = $_SERVER["REQUEST_METHOD"];
    $parts = explode('/', trim($route, '/'));

    if ($parts[0] == "comments") {
        switch ($request_method) {
            case 'GET':
                if (isset($parts[1])) {
                    $controller->getComments($parts[1]);
                } else {
                    jsonResponse(["success" => false, "message" => "Post ID required"], 400);
                }
                break;

            case 'POST':
                $data = getJsonBody();
                $controller->addComment($data);
                break;

            case 'PUT':
                if (isset($parts[1])) {
                    $data = getJsonBody();
                    $controller->updateComment($parts[1], $data);
                } else {
                    jsonResponse(["success" => false, "message" => "Comment ID required"], 400);
                }
                break;

            case 'DELETE':
                if (isset($parts[1])) {
                    $controller->deleteComment($parts[1]);
                } else {
                    jsonResponse(["success" => false, "message" => "Comment ID required"], 400);
                }
                break;

            case 'PATCH':
                if (isset($parts[1], $parts[2]) && $parts[2] === "approve") {
                    $controller->approveComment($parts[1]);
                } elseif (isset($parts[1], $parts[2]) && $parts[2] === "reject") {
                    $controller->rejectComment($parts[1]);
                } else {
                    jsonResponse(["success" => false, "message" => "Invalid action"], 400);
                }
                break;

            default:
                jsonResponse(["success" => false, "message" => "Method not allowed"], 405);
        }
    }
}
