<?php
require_once __DIR__ . '/../models/subject.php';

class SubjectService {
    private $subject;
    
    public function __construct() {
        $this->subject = new Subject();
    }
    
    /**
     * Get all subjects
     * 
     * @param string $orderBy Field to order by
     * @param string $order Order direction (ASC/DESC)
     * @param int $limit Number of records to return (0 = all)
     * @param int $offset Starting position of records
     * @param bool $activeOnly Whether to get only active subjects
     * @return array Response with status and data
     */
    public function getAllSubjects($orderBy = 'created_at', $order = 'DESC', $limit = 0, $offset = 0, $activeOnly = false) {
        if ($activeOnly) {
            $subjects = $this->subject->getActive($orderBy, $order, $limit, $offset);
            $total = $this->subject->countAll(true);
        } else {
            $subjects = $this->subject->getAll($orderBy, $order, $limit, $offset);
            $total = $this->subject->countAll();
        }
        
        return [
            'status' => 200,
            'message' => 'Subjects retrieved successfully',
            'data' => [
                'subjects' => $subjects,
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset
            ]
        ];
    }
    
    /**
     * Get a single subject by ID
     * 
     * @param int $id Subject ID
     * @return array Response with status and data
     */
    public function getSubjectById($id) {
        // Check if ID is valid
        if (!$id || !is_numeric($id)) {
            return [
                'status' => 400,
                'message' => 'Valid subject ID is required'
            ];
        }
        
        // Get subject
        if (!$this->subject->getById($id)) {
            return [
                'status' => 404,
                'message' => 'Subject not found'
            ];
        }
        
        return [
            'status' => 200,
            'message' => 'Subject retrieved successfully',
            'data' => $this->subject->toArray()
        ];
    }
    
    /**
     * Create a new subject
     * 
     * @param string $subject_name Subject name
     * @param string $content_subject Subject content
     * @param int $status Subject status (1 = active, 0 = inactive)
     * @return array Response with status and data
     */
    public function createSubject($subject_name, $content_subject = '', $status = 1) {
        if (empty(trim($subject_name))) {
            return [
                'status' => 400,
                'message' => 'Subject name is required'
            ];
        }
        
        // Check if subject name already exists
        if ($this->subject->nameExists($subject_name)) {
            return [
                'status' => 409,
                'message' => 'Subject with this name already exists'
            ];
        }
        
        // Create subject
        if ($this->subject->create($subject_name, $content_subject, $status)) {
            return [
                'status' => 201,
                'message' => 'Subject created successfully',
                'data' => $this->subject->toArray()
            ];
        }
        
        return [
            'status' => 500,
            'message' => 'Failed to create subject'
        ];
    }
    
    /**
     * Update a subject
     * 
     * @param int $id Subject ID
     * @param array $data Update data (subject_name, content_subject, status)
     * @return array Response with status and data
     */
    public function updateSubject($id, $data) {
        // Check if ID is valid
        if (!$id || !is_numeric($id)) {
            return [
                'status' => 400,
                'message' => 'Valid subject ID is required'
            ];
        }
        
        // Check if subject exists
        if (!$this->subject->getById($id)) {
            return [
                'status' => 404,
                'message' => 'Subject not found'
            ];
        }
        
        // Check if subject name exists (if name is being updated)
        if (isset($data['subject_name']) && $data['subject_name'] !== $this->subject->subject_name) {
            if ($this->subject->nameExists($data['subject_name'])) {
                return [
                    'status' => 409,
                    'message' => 'Subject with this name already exists'
                ];
            }
        }
        
        // Prepare update data
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
        
        // If no fields to update
        if (empty($updateData)) {
            return [
                'status' => 400,
                'message' => 'No data to update'
            ];
        }
        
        // Update subject
        if ($this->subject->update($id, $updateData)) {
            return [
                'status' => 200,
                'message' => 'Subject updated successfully',
                'data' => $this->subject->toArray()
            ];
        }
        
        return [
            'status' => 500,
            'message' => 'Failed to update subject'
        ];
    }
    
    /**
     * Delete a subject
     * 
     * @param int $id Subject ID
     * @return array Response with status and message
     */
    public function deleteSubject($id) {
        // Check if ID is valid
        if (!$id || !is_numeric($id)) {
            return [
                'status' => 400,
                'message' => 'Valid subject ID is required'
            ];
        }
        
        // Check if subject exists
        if (!$this->subject->getById($id)) {
            return [
                'status' => 404,
                'message' => 'Subject not found'
            ];
        }
        
        // Delete subject
        if ($this->subject->delete($id)) {
            return [
                'status' => 200,
                'message' => 'Subject deleted successfully'
            ];
        }
        
        return [
            'status' => 500,
            'message' => 'Failed to delete subject'
        ];
    }
    
    /**
     * Toggle subject status
     * 
     * @param int $id Subject ID
     * @return array Response with status and data
     */
    public function toggleSubjectStatus($id) {
        // Check if ID is valid
        if (!$id || !is_numeric($id)) {
            return [
                'status' => 400,
                'message' => 'Valid subject ID is required'
            ];
        }
        
        // Check if subject exists
        if (!$this->subject->getById($id)) {
            return [
                'status' => 404,
                'message' => 'Subject not found'
            ];
        }
        
        // Toggle status
        if ($this->subject->toggleStatus($id)) {
            return [
                'status' => 200,
                'message' => 'Subject status updated successfully',
                'data' => [
                    'id' => $id,
                    'status' => $this->subject->status
                ]
            ];
        }
        
        return [
            'status' => 500,
            'message' => 'Failed to update subject status'
        ];
    }
    
    /**
     * Search subjects
     * 
     * @param string $keyword Search keyword
     * @param int $limit Number of records to return
     * @param int $offset Starting position of records
     * @return array Response with status and data
     */
    public function searchSubjects($keyword, $limit = 0, $offset = 0) {
        if (empty(trim($keyword))) {
            return [
                'status' => 400,
                'message' => 'Search keyword is required'
            ];
        }
        
        $subjects = $this->subject->search($keyword, $limit, $offset);
        
        return [
            'status' => 200,
            'message' => 'Search results retrieved successfully',
            'data' => [
                'subjects' => $subjects,
                'count' => count($subjects),
                'keyword' => $keyword
            ]
        ];
    }
}