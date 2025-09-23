# Subject API Documentation

## Overview

This documentation covers the Subject API endpoints for managing blog subjects/categories. The API provides comprehensive CRUD operations and search functionality for blog subjects.

## Base URL

All API endpoints are relative to the base URL:

```
http://webblog223.test/backend
```

## Authentication

Most Subject API endpoints require authentication. The API uses JWT (JSON Web Token) for authentication.

- **Public endpoints**: No authentication required
- **Protected endpoints**: Require a valid JWT token
- **Admin-only endpoints**: Require a valid JWT token with admin role

Include the JWT token in the Authorization header for protected endpoints:

```
Authorization: Bearer <your_jwt_token>
```

## Response Format

All API responses are in JSON format and include:

- `status`: HTTP status code
- `message`: Description of the result
- `data`: Response payload (when applicable)

Example success response:
```json
{
  "status": 200,
  "message": "Subjects retrieved successfully",
  "data": {
    "subjects": [...]
  }
}
```

Example error response:
```json
{
  "status": 404,
  "message": "Subject not found"
}
```

## Subject Object Structure

| Field           | Type      | Description                              |
|-----------------|-----------|------------------------------------------|
| id              | Integer   | Unique identifier                        |
| subject_name    | String    | Name of the subject                      |
| content_subject | String    | Detailed description of the subject      |
| status          | Integer   | 1: Active, 0: Inactive                   |
| created_at      | Timestamp | Creation date and time                   |
| updated_at      | Timestamp | Last update date and time                |

---

## Endpoints

### Public Endpoints

#### 1. Get All Subjects

Retrieves a list of all subjects with optional filtering and pagination.

- **URL**: `/subjects`
- **Method**: `GET`
- **Authentication**: None

**Query Parameters:**

| Parameter | Type   | Required | Default    | Description                                   |
|-----------|--------|----------|------------|-----------------------------------------------|
| order_by  | String | No       | created_at | Field to order results by                     |
| order     | String | No       | DESC       | Order direction (ASC or DESC)                 |
| limit     | Integer| No       | 0 (all)    | Number of records to return                   |
| offset    | Integer| No       | 0          | Number of records to skip                     |
| active    | Boolean| No       | false      | If 'true', return only active subjects        |

**Success Response (200 OK):**

```json
{
  "status": 200,
  "message": "Subjects retrieved successfully",
  "data": {
    "subjects": [
      {
        "id": 1,
        "subject_name": "Technology",
        "content_subject": "Articles about technology trends and innovations",
        "status": 1,
        "created_at": "2025-09-15 10:30:00",
        "updated_at": "2025-09-15 10:30:00"
      },
      {
        "id": 2,
        "subject_name": "Travel",
        "content_subject": "Travel guides and experiences",
        "status": 1,
        "created_at": "2025-09-15 11:45:00",
        "updated_at": "2025-09-15 11:45:00"
      }
    ],
    "total": 2,
    "limit": 10,
    "offset": 0
  }
}
```

**Example:**
```
GET /subjects?order_by=subject_name&order=ASC&limit=10&offset=0&active=true
```

#### 2. Get Subject by ID

Retrieves a single subject by its ID.

- **URL**: `/subjects/{id}`
- **Method**: `GET`
- **Authentication**: None

**Path Parameters:**

| Parameter | Type    | Required | Description         |
|-----------|---------|----------|---------------------|
| id        | Integer | Yes      | ID of the subject   |

**Success Response (200 OK):**

```json
{
  "status": 200,
  "message": "Subject retrieved successfully",
  "data": {
    "id": 1,
    "subject_name": "Technology",
    "content_subject": "Articles about technology trends and innovations",
    "status": 1,
    "created_at": "2025-09-15 10:30:00",
    "updated_at": "2025-09-15 10:30:00"
  }
}
```

**Error Responses:**

- **400 Bad Request**: Subject ID is required
- **404 Not Found**: Subject not found

**Example:**
```
GET /subjects/1
```

#### 3. Search Subjects

Searches for subjects based on a keyword.

- **URL**: `/subjects/search`
- **Method**: `GET`
- **Authentication**: None

**Query Parameters:**

| Parameter | Type    | Required | Default | Description                    |
|-----------|---------|----------|---------|--------------------------------|
| keyword   | String  | Yes      | -       | Search term                    |
| limit     | Integer | No       | 0 (all) | Number of records to return    |
| offset    | Integer | No       | 0       | Number of records to skip      |

**Success Response (200 OK):**

```json
{
  "status": 200,
  "message": "Search results retrieved successfully",
  "data": {
    "subjects": [
      {
        "id": 1,
        "subject_name": "Technology",
        "content_subject": "Articles about technology trends and innovations",
        "status": 1,
        "created_at": "2025-09-15 10:30:00",
        "updated_at": "2025-09-15 10:30:00"
      }
    ],
    "count": 1,
    "keyword": "tech"
  }
}
```

**Error Response:**

- **400 Bad Request**: Search keyword is required

**Example:**
```
GET /subjects/search?keyword=tech&limit=10&offset=0
```

### Admin-Only Endpoints

#### 4. Create Subject

Creates a new subject.

- **URL**: `/subjects`
- **Method**: `POST`
- **Authentication**: Admin-only
- **Content Type**: `application/json`

**Request Body:**

| Field           | Type    | Required | Default | Description                        |
|-----------------|---------|----------|---------|------------------------------------|
| subject_name    | String  | Yes      | -       | Name of the subject                |
| content_subject | String  | No       | ""      | Detailed description of the subject|
| status          | Integer | No       | 1       | 1: Active, 0: Inactive             |

**Success Response (201 Created):**

```json
{
  "status": 201,
  "message": "Subject created successfully",
  "data": {
    "id": 3,
    "subject_name": "Health",
    "content_subject": "Health and wellness articles",
    "status": 1,
    "created_at": "2025-09-19 14:30:00",
    "updated_at": "2025-09-19 14:30:00"
  }
}
```

**Error Responses:**

- **400 Bad Request**: Subject name is required
- **409 Conflict**: Subject with this name already exists
- **500 Internal Server Error**: Failed to create subject

**Example Request:**
```json
POST /subjects
{
  "subject_name": "Health",
  "content_subject": "Health and wellness articles",
  "status": 1
}
```

#### 5. Update Subject

Updates an existing subject.

- **URL**: `/subjects/{id}`
- **Method**: `PUT`
- **Authentication**: Admin-only
- **Content Type**: `application/json`

**Path Parameters:**

| Parameter | Type    | Required | Description       |
|-----------|---------|----------|-------------------|
| id        | Integer | Yes      | ID of the subject |

**Request Body:**

| Field           | Type    | Required | Description                         |
|-----------------|---------|----------|-------------------------------------|
| subject_name    | String  | No       | New name of the subject             |
| content_subject | String  | No       | New description of the subject      |
| status          | Integer | No       | New status (1: Active, 0: Inactive) |

**Success Response (200 OK):**

```json
{
  "status": 200,
  "message": "Subject updated successfully",
  "data": {
    "id": 1,
    "subject_name": "Technology & Innovation",
    "content_subject": "Articles about cutting-edge technology",
    "status": 1,
    "created_at": "2025-09-15 10:30:00",
    "updated_at": "2025-09-19 15:45:00"
  }
}
```

**Error Responses:**

- **400 Bad Request**: Subject ID is required or No data to update
- **404 Not Found**: Subject not found
- **409 Conflict**: Subject with this name already exists
- **500 Internal Server Error**: Failed to update subject

**Example Request:**
```json
PUT /subjects/1
{
  "subject_name": "Technology & Innovation",
  "content_subject": "Articles about cutting-edge technology"
}
```

#### 6. Delete Subject

Deletes a subject by its ID.

- **URL**: `/subjects/{id}`
- **Method**: `DELETE`
- **Authentication**: Admin-only

**Path Parameters:**

| Parameter | Type    | Required | Description       |
|-----------|---------|----------|-------------------|
| id        | Integer | Yes      | ID of the subject |

**Success Response (200 OK):**

```json
{
  "status": 200,
  "message": "Subject deleted successfully"
}
```

**Error Responses:**

- **400 Bad Request**: Subject ID is required
- **404 Not Found**: Subject not found
- **500 Internal Server Error**: Failed to delete subject

**Example:**
```
DELETE /subjects/3
```

#### 7. Toggle Subject Status

Toggles the status of a subject between active (1) and inactive (0).

- **URL**: `/subjects/status/{id}`
- **Method**: `PATCH`
- **Authentication**: Admin-only

**Path Parameters:**

| Parameter | Type    | Required | Description       |
|-----------|---------|----------|-------------------|
| id        | Integer | Yes      | ID of the subject |

**Success Response (200 OK):**

```json
{
  "status": 200,
  "message": "Subject status updated successfully",
  "data": {
    "id": 2,
    "status": 0
  }
}
```

**Error Responses:**

- **400 Bad Request**: Subject ID is required
- **404 Not Found**: Subject not found
- **500 Internal Server Error**: Failed to update subject status

**Example:**
```
PATCH /subjects/status/2
```

## Error Codes

| Status Code | Description                                      |
|-------------|--------------------------------------------------|
| 200         | OK - The request was successful                  |
| 201         | Created - Resource created successfully          |
| 400         | Bad Request - Invalid parameters                 |
| 401         | Unauthorized - Authentication required           |
| 403         | Forbidden - Insufficient permissions             |
| 404         | Not Found - Resource not found                   |
| 409         | Conflict - Resource conflict (e.g., duplicate)   |
| 500         | Internal Server Error - Server-side error        |

## Testing the API

You can use the provided `test_subjects_api.php` script to test all the API endpoints. This script demonstrates how to:

1. Get an admin token via login
2. Get all subjects
3. Create a new subject
4. Get a subject by ID
5. Update a subject
6. Toggle subject status
7. Search for subjects
8. Delete a subject

To run the test script:
```
php test_subjects_api.php
```

Make sure to update the admin credentials in the script before running it.

## Database Schema

The subjects table has the following structure:

```sql
CREATE TABLE IF NOT EXISTS subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_name VARCHAR(255) NOT NULL,
    content_subject TEXT,
    status TINYINT(1) DEFAULT 1 COMMENT '1: Active, 0: Inactive',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Implementation Details

The Subject API is implemented with a clean architecture that separates concerns:

1. **Routes**: Handle URL routing and HTTP method mapping
2. **Controllers**: Process HTTP requests and responses
3. **Services**: Contain the business logic
4. **Models**: Handle database operations

This separation makes the codebase more maintainable and testable.

## Conclusion

This API provides a complete set of operations for managing blog subjects. It follows RESTful principles and includes proper validation, error handling, and authentication controls to ensure secure and reliable operation.