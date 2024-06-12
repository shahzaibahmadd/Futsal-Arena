<?php
include 'config.php';
// Create connection


// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect post data
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $message = $conn->real_escape_string($_POST['message']);
    
    // Prepare SQL statement
    $sql = "INSERT INTO ContactSubmissions (name, email, message) VALUES ('$name', '$email', '$message')";
    
    // Execute the query
    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    
    // Close the connection
    $conn->close();
}

header('Location: index.html');
?>


