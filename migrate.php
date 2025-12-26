<?php
// Database migration script - Add address columns to users table
// This script should be run once to add the necessary columns for checkout functionality

header('Content-Type: application/json');

include './partials/database.php';

try {
    // Check if columns already exist
    $check_sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
                  WHERE TABLE_NAME = 'users' AND COLUMN_NAME = 'address_line1'";
    $result = $conn->query($check_sql);
    
    if ($result && $result->num_rows > 0) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Address columns already exist'
        ]);
        exit;
    }
    
    // Add address columns if they don't exist
    $alter_sql = "ALTER TABLE users 
                  ADD COLUMN address_line1 VARCHAR(255) DEFAULT NULL,
                  ADD COLUMN address_line2 VARCHAR(255) DEFAULT NULL,
                  ADD COLUMN city VARCHAR(100) DEFAULT NULL,
                  ADD COLUMN state VARCHAR(100) DEFAULT NULL,
                  ADD COLUMN postal_code VARCHAR(20) DEFAULT NULL,
                  ADD COLUMN country VARCHAR(100) DEFAULT NULL";
    
    if ($conn->query($alter_sql)) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Address columns added successfully'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to add columns: ' . $conn->error
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
