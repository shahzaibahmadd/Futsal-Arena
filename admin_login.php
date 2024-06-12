<?php
require 'config.php'; // Include your database connection file
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_email = $_POST['admin_email'] ?? '';
    $admin_password = $_POST['admin_password'] ?? '';

    // Validate input
    if (empty($admin_email) || empty($admin_password)) {
        $error_message = "Email and password are required.";
    } else {
        $sql = "SELECT id, username, password FROM admin_users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die('MySQL prepare error: ' . $conn->error);
        }

        $stmt->bind_param("s", $admin_email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            if (password_verify($admin_password, $admin['password'])) {
                // Set session variables
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];

                // Redirect to the Admin Dashboard after successful login
                header('Location: dashboard.php');
                exit();
            } else {
                $error_message = "Invalid password.";
            }
        } else {
            $error_message = "Admin does not exist.";
        }
        $stmt->close();
    }
    $conn->close();
}

// Display error message if set
if (isset($error_message)) {
    echo "<script>alert('$error_message');</script>";
}
?>
