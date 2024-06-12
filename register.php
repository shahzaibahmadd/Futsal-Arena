<?php
require 'config.php';
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // var_dump($_POST);  // Debugging output

    // Extracting data from the POST array
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Optional: Validate the data (e.g., check if passwords match)
    if ($password !== $confirm_password) {
        die('Passwords do not match.');
    }

    // Check if the user already exists
    $check_user_sql = "SELECT * FROM users WHERE email = ?";
    $check_stmt = $conn->prepare($check_user_sql);
    if ($check_stmt === false) {
        die('MySQL prepare error: ' . $conn->error);
    }
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo "User already exists";
        $check_stmt->close();
        $conn->close();
        exit;
    }

    // Hashing the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // SQL statement to insert a new record
    $sql = "INSERT INTO users (name, email, mobile, password) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die('MySQL prepare error: ' . $conn->error);
    }

    // Binding parameters and executing the statement
    $stmt->bind_param("ssss", $name, $email, $mobile, $hashed_password);
    if ($stmt->execute()) {
        echo "User registered successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Closing the statement and connection
    $stmt->close();
    $conn->close();
}
?>
