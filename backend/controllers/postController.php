<?php
require_once __DIR__ . '/../services/postService.php';

class PostController {
    private $postService;

    public function __construct() {
        $this->postService = new PostService();
    }

    // Public
    public function getAll() {
        return $this->postService->getPosts($_GET);
    }

    public function getById($id) {
        return $this->postService->getPostById($id);
    }

    // User
    public function create($userId) {
        $data = json_decode(file_get_contents('php://input'), true);
        return $this->postService->createPost($data, $userId);
    }

    public function update($id, $requestingUserId, $userRole) {
        $data = json_decode(file_get_contents('php://input'), true);
        return $this->postService->updatePost($id, $data, $requestingUserId, $userRole);
    }

    public function delete($id, $requestingUserId, $userRole) {
        return $this->postService->deletePost($id, $requestingUserId, $userRole);
    }

    public function like($postId, $userId) {
        $data = json_decode(file_get_contents('php://input'), true);
        $likeStatus = $data['like_status'] ?? 0; // 1 for like, -1 for dislike
        return $this->postService->likePost($postId, $userId, $likeStatus);
    }

    // Admin
    public function manageStatus($id, $userRole) {
        $data = json_decode(file_get_contents('php://input'), true);
        $status = $data['status'] ?? 0;
        return $this->postService->managePostStatus($id, $status, $userRole);
    }

    public function getAllForAdmin() {
        return $this->postService->getAdminPosts($_GET);
    }
}
