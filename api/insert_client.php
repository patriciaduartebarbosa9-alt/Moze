<?php
// Include the database configuration
require_once 'db.inc';

try {
    // Check if the request method is POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $rawData = file_get_contents('php://input');
        $data = json_decode($rawData, true);

        // Get the form data from JSON
        $name = isset($data['name']) ? trim($data['name']) : '';
        $nif = isset($data['nif']) ? trim($data['nif']) : '';
        // $name ="ana";
        // $nif ="12";   
        // Validate the data
        if (empty($name) || empty($nif)) {
            http_response_code(400);
            echo json_encode(['message' => 'Name and NIF are required.']);
            exit;
        }

        // Check if NIF already exists
        $checkStmt = $conn->prepare("SELECT COUNT(*) FROM clients WHERE nif = ?");
        if (!$checkStmt) {
            throw new Exception('Failed to prepare check statement: ' . $conn->error);
        }
        $checkStmt->bind_param("i", $nif);
        $checkStmt->execute();
        $checkStmt->bind_result($count);
        $checkStmt->fetch();
        $checkStmt->close();

        if ($count > 0) {
            http_response_code(400);
            echo json_encode(['message' => 'The provided NIF already exists in the database. Please use a unique NIF.']);
            exit;
        }

        // Prepare and bind the SQL statement
        $stmt = $conn->prepare("INSERT INTO clients (name, nif) VALUES (?, ?)");
        if (!$stmt) {
            throw new Exception('Failed to prepare statement: ' . $conn->error);
        }
        $stmt->bind_param("si", $name, $nif);

        // Execute the statement
        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode(['message' => 'Client data inserted successfully.']);
        } else {
            throw new Exception('Failed to insert data: ' . $stmt->error);
        }
        // Close the statement and connection
        $stmt->close();
        $conn->close();
    } else {
        // Respond with an error if the request method is not POST
        http_response_code(405);
        echo json_encode(['message' => 'Invalid request method.']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['message' => $e->getMessage()]);
}