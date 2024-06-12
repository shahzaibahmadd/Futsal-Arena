<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Retrieve logged-in user's transactions
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM Transactions WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Transactions</title>
    <link rel="stylesheet" href="style.css"> <!-- Assuming you have a CSS file for styling -->
</head>
<body>
    <header>
    <h1>Welcome to Your Transcation History, <?php echo $_SESSION['username']; ?>!</h1>
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
        </nav>
    </header>
    <main>
        <div class="container">
            <h2>User Transactions</h2>
            <table>
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Booking ID</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['transaction_id']; ?></td>
                            <td><?php echo $row['booking_id']; ?></td>
                            <td><?php echo $row['amount']; ?></td>
                            <td><?php echo $row['payment_method']; ?></td>
                            <td><?php echo $row['status']; ?></td>
                            <td><?php echo $row['timestamp']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
    <footer>
    <p>Copyright © 2023 Futsal Range</p>
    </footer>
</body>
</html>
