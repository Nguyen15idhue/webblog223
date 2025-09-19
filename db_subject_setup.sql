-- Create subjects table for blog topics
CREATE TABLE IF NOT EXISTS subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_name VARCHAR(255) NOT NULL,
    content_subject TEXT,
    status TINYINT(1) DEFAULT 1 COMMENT '1: Active, 0: Inactive',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add some sample subjects
INSERT INTO subjects (subject_name, content_subject, status) VALUES
('Technology', 'Articles about the latest technology trends, gadgets, and innovations', 1),
('Travel', 'Travel guides, tips, and experiences from around the world', 1),
('Food', 'Recipes, restaurant reviews, and culinary experiences', 1),
('Health', 'Health tips, fitness advice, and wellness information', 1),
('Business', 'Business news, entrepreneurship, and career advice', 1);