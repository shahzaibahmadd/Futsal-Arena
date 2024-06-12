<?php
session_start();
require 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Update user data
    $update_stmt = $conn->prepare("UPDATE users SET name = ?, mobile = ?, email = ?, password = ? WHERE id = ?");
    $update_stmt->bind_param("ssssi", $name, $mobile, $email, $hashed_password, $user_id);
    $update_stmt->execute();

    // Fetch updated user data
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>User Dashboard</h1>
        <nav>
        <ul>
        <li><button><a href="user_dashboard.php">Home</a></button></li>
                <li><button><a href="booknow.php">Book Now</a></button></li>
                <li><button><a href="profile.php">Profile</a></button></li>
                <li><button><a href="user_history.php">Booking History</a></button></li>
                <li><button><a href="user_wallet.php">My wallet</a></button></li>
                <li><button><a href="user_createwallet.php">Create wallet</a></button></li>
                <li><button><a href="user_transaction.php">My transactions</a></button></li>
                <li><button><a href="user_submitreview.php">Submit Review</a></button></li>
                <li><button><a href="index.html">Logout</a></button></li>
        </ul>
    </nav>
    </header>

  

    <main>
        <div class="site-content">
            <div class="forms-container">
                <div class="form">
                    <h2>User Profile</h2>
                    <form method="POST">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

                        <label for="mobile">Mobile:</label>
                        <input type="text" id="mobile" name="mobile" value="<?php echo htmlspecialchars($user['mobile']); ?>" required>

                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>

                        <button type="submit">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <footer>
    <p>Copyright © 2023 Futsal Range</p>
    </footer>
</body>
</html>
