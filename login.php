<?php
require 'config.php';
session_start();

// Uncomment this if you want to redirect already logged-in users
// if (isset($_SESSION['user_id'])) {
//     header('Location: booknow.php'); // Redirect to booking page if already logged in
//     exit();
// }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validate input
    if (empty($email) || empty($password)) {
        $error_message = "Email and password are required.";
    } else {
        $sql = "SELECT id, name, password FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die('MySQL prepare error: ' . $conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['name']; // Changed to 'name' from 'username'

                // Redirect to the Book Now page after successful login
                header('Location: user_dashboard.php');
                exit();
            } else {
                $error_message = "Invalid password.";
               

            }
        } else {
            $error_message = "User does not exist.";
        

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
