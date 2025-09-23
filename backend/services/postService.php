<?php
require_once __DIR__ . '/../models/post.php';
require_once __DIR__ . '/../models/subject.php';

class PostService {
    private $post;

    public function __construct() {
        $this->post = new Post();
    }

    public function getPosts($params) {
        $orderBy = $params['order_by'] ?? 'created_at';
        $order = $params['order'] ?? 'DESC';
        $limit = $params['limit'] ?? 10;
        $offset = $params['offset'] ?? 0;
        $keyword = $params['keyword'] ?? null;
        $userId = $params['user_id'] ?? null;
        $subjectId = $params['subject_id'] ?? null;
        
        $posts = $this->post->getAll($orderBy, $order, $limit, $offset, $keyword, $userId, $subjectId);
        $total = $this->post->countAll($keyword, $userId, $subjectId);

        return ['status' => 200, 'data' => ['posts' => $posts, 'total' => $total]];
    }

    public function getPostById($id) {
        if (!$id || !is_numeric($id)) {
            return ['status' => 400, 'message' => 'Valid post ID is required'];
        }
        if (!$this->post->getById($id)) {
            return ['status' => 404, 'message' => 'Post not found'];
        }
        return ['status' => 200, 'data' => $this->post->toArray()];
    }

    public function createPost($data, $userId) {
        if (empty($data['title']) || empty($data['content']) || empty($data['subject_id'])) {
            return ['status' => 400, 'message' => 'Title, content, and subject_id are required'];
        }

        $subject = new Subject();
        if (!$subject->getById($data['subject_id'])) {
            return ['status' => 404, 'message' => 'Subject not found'];
        }

        if ($this->post->create($userId, $data['subject_id'], $data['title'], $data['content'])) {
            return ['status' => 201, 'message' => 'Post created successfully', 'data' => $this->post->toArray()];
        }
        return ['status' => 500, 'message' => 'Failed to create post'];
    }

    public function updatePost($id, $data, $requestingUserId, $userRole) {
        $postData = $this->getPostById($id);
        if ($postData['status'] !== 200) return $postData;
        
        if ($userRole !== 'admin' && $this->post->user_id != $requestingUserId) {
            return ['status' => 403, 'message' => 'Forbidden: You can only update your own posts'];
        }

        if (isset($data['subject_id'])) {
            $subject = new Subject();
            if (!$subject->getById($data['subject_id'])) {
                return ['status' => 404, 'message' => 'Subject not found'];
            }
        }

        if ($this->post->update($id, $data)) {
            $this->post->getById($id);
            return ['status' => 200, 'message' => 'Post updated successfully', 'data' => $this->post->toArray()];
        }
        return ['status' => 500, 'message' => 'Failed to update post'];
    }

    public function deletePost($id, $requestingUserId, $userRole) {
        $postData = $this->getPostById($id);
        if ($postData['status'] !== 200) return $postData;

        if ($userRole !== 'admin' && $this->post->user_id != $requestingUserId) {
            return ['status' => 403, 'message' => 'Forbidden: You can only delete your own posts'];
        }

        if ($this->post->delete($id)) {
            return ['status' => 200, 'message' => 'Post deleted successfully'];
        }
        return ['status' => 500, 'message' => 'Failed to delete post'];
    }

    public function managePostStatus($id, $status, $userRole) {
        if ($userRole !== 'admin') {
            return ['status' => 403, 'message' => 'Forbidden: Only admins can manage post status'];
        }
        $postData = $this->getPostById($id);
        if ($postData['status'] !== 200) return $postData;

        if ($this->post->update($id, ['status' => $status])) {
            return ['status' => 200, 'message' => 'Post status updated successfully'];
        }
        return ['status' => 500, 'message' => 'Failed to update post status'];
    }

    public function likePost($postId, $userId, $likeStatus) {
        $postData = $this->getPostById($postId);
        if ($postData['status'] !== 200) return $postData;

        if ($this->post->likeOrDislike($postId, $userId, $likeStatus)) {
            return ['status' => 200, 'message' => 'Action successful'];
        }
        return ['status' => 500, 'message' => 'Failed to perform action'];
    }

    public function getAdminPosts($params) {
        $orderBy = $params['order_by'] ?? 'created_at';
        $order = $params['order'] ?? 'DESC';
        $limit = $params['limit'] ?? 10;
        $offset = $params['offset'] ?? 0;
        
        $posts = $this->post->getAll($orderBy, $order, $limit, $offset, null, null, null, false);
        $total = $this->post->countAll(null, null, null, false);

        return ['status' => 200, 'data' => ['posts' => $posts, 'total' => $total]];
    }
}
