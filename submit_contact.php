<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); 
define('DB_PASSWORD', ''); 
define('DB_NAME', 'portfolio_db'); 


$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);


if ($conn->connect_error) {

    error_log("Connection failed: " . $conn->connect_error);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database connection error. Please try again later.']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $conn->real_escape_string(trim($_POST['name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $message = $conn->real_escape_string(trim($_POST['message']));

    if (empty($name) || empty($email) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Please provide valid name, email, and message.']);
        exit();
    }

    $sql = "INSERT INTO contact_messages (sender_name, sender_email, message_text) VALUES (?, ?, ?)";
    
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters: 'sss' means three string types
        $stmt->bind_param("sss", $name, $email, $message);
        
        // Execute the statement
        if ($stmt->execute()) {
            // Success response
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true, 
                'message' => "Thank you, $name! Your message has been sent successfully."
            ]);
        } else {
            // Execution error
            error_log("Execute failed: " . $stmt->error);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'An error occurred during submission.']);
        }

        // Close statement
        $stmt->close();
    } else {
        // Prepare error
        error_log("Prepare failed: " . $conn->error);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Server error, cannot prepare statement.']);
    }


    $conn->close();

} else {

    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>