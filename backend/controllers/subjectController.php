<?php
require_once __DIR__ . '/../services/subjectService.php';

class SubjectController {
    private $subjectService;
    
    public function __construct() {
        $this->subjectService = new SubjectService();
    }
    
    /**
     * Get all subjects
     * 
     * @return array Response
     */
    public function getAll() {
        $orderBy = isset($_GET['order_by']) ? $_GET['order_by'] : 'created_at';
        $order = isset($_GET['order']) ? $_GET['order'] : 'DESC';
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 0;
        $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
        
        // Get only active subjects if specified
        $activeOnly = isset($_GET['active']) && $_GET['active'] === 'true';
        
        // Delegate to service
        return $this->subjectService->getAllSubjects($orderBy, $order, $limit, $offset, $activeOnly);
    }
    
    /**
     * Get a single subject by ID
     * 
     * @return array Response
     */
    public function getById() {
        // Check if ID is provided
        if (!isset($_GET['id'])) {
            return [
                'status' => 400,
                'message' => 'Subject ID is required'
            ];
        }
        
        $id = (int)$_GET['id'];
        
        // Delegate to service
        return $this->subjectService->getSubjectById($id);
    }
    
    /**
     * Create a new subject
     * 
     * @return array Response
     */
    public function create() {
        // Get POST data
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['subject_name'])) {
            return [
                'status' => 400,
                'message' => 'Subject name is required'
            ];
        }
        
        $subject_name = $data['subject_name'];
        $content_subject = isset($data['content_subject']) ? $data['content_subject'] : '';
        $status = isset($data['status']) ? (int)$data['status'] : 1;
        
        // Delegate to service
        return $this->subjectService->createSubject($subject_name, $content_subject, $status);
    }
    
    /**
     * Update a subject
     * 
     * @return array Response
     */
    public function update() {
        // Get PUT data
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['id'])) {
            return [
                'status' => 400,
                'message' => 'Subject ID is required'
            ];
        }
        
        $id = (int)$data['id'];
        
        // Extract update data
        $updateData = [];
        if (isset($data['subject_name'])) {
            $updateData['subject_name'] = $data['subject_name'];
        }
        
        if (isset($data['content_subject'])) {
            $updateData['content_subject'] = $data['content_subject'];
        }
        
        if (isset($data['status'])) {
            $updateData['status'] = (int)$data['status'];
        }
        
        // Delegate to service
        return $this->subjectService->updateSubject($id, $updateData);
    }
    
    /**
     * Delete a subject
     * 
     * @return array Response
     */
    public function delete() {
        // Get DELETE data
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['id'])) {
            return [
                'status' => 400,
                'message' => 'Subject ID is required'
            ];
        }
        
        $id = (int)$data['id'];
        
        // Delegate to service
        return $this->subjectService->deleteSubject($id);
    }
    
    /**
     * Toggle subject status
     * 
     * @return array Response
     */
    public function toggleStatus() {
        // Get PATCH data
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['id'])) {
            return [
                'status' => 400,
                'message' => 'Subject ID is required'
            ];
        }
        
        $id = (int)$data['id'];
        
        // Delegate to service
        return $this->subjectService->toggleSubjectStatus($id);
    }
    
    /**
     * Search subjects
     * 
     * @return array Response
     */
    public function search() {
        if (!isset($_GET['keyword']) || empty(trim($_GET['keyword']))) {
            return [
                'status' => 400,
                'message' => 'Search keyword is required'
            ];
        }
        
        $keyword = $_GET['keyword'];
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 0;
        $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
        
        // Delegate to service
        return $this->subjectService->searchSubjects($keyword, $limit, $offset);
    }
}