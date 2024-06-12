
<?php
// Start session and check if user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

// Include database connection
require 'config.php';

// Fetch user's wallet information from the database
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM Wallet WHERE user_id = $user_id";
$result = $conn->query($query);

// Check if the user has a wallet
if ($result->num_rows > 0) {
    $wallet = $result->fetch_assoc();
    $wallet_id = $wallet['wallet_id'];
    $balance = $wallet['balance'];
    $created_at = $wallet['created_at'];
} else {
    // Redirect to a page to create a wallet if the user doesn't have one
    header('Location: user_createwallet.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wallet</title>
    <!-- Add your CSS stylesheets here -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Welcome to Your Wallet, <?php echo $_SESSION['username']; ?>!</h1>
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
        <section>
            <h2>My Wallet</h2>
            <table>
                <tr>
                    <th>Wallet ID</th>
                    <th>Balance</th>
                    <th>Created At</th>
                </tr>
                <tr>
                    <td><?php echo $wallet_id; ?></td>
                    <td>$<?php echo $balance; ?></td>
                    <td><?php echo $created_at; ?></td>
                </tr>
            </table>
        </section>
        
        <!-- Add more sections with relevant content -->
    </main>

    <footer>
        <p>Copyright © 2023 Futsal Range</p>
    </footer>
</body>
</html>
