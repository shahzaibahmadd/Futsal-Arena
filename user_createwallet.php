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
    $wallet_exists = true;
    $wallet = $result->fetch_assoc();
    $wallet_id = $wallet['wallet_id'];
    $balance = $wallet['balance'];
    $created_at = $wallet['created_at'];
} else {
    $wallet_exists = false;
}

// Handle wallet deletion
if (isset($_POST['delete_wallet'])) {
    $delete_query = "DELETE FROM Wallet WHERE user_id = $user_id";
    if ($conn->query($delete_query) === TRUE) {
        echo '<script>alert("Wallet deleted successfully!");</script>';
        header('Location: user_createwallet.php');
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// Handle wallet creation
if (!$wallet_exists && isset($_POST['create_wallet'])) {
    $balance = $_POST['balance'];
    $create_query = "INSERT INTO Wallet (user_id, balance) VALUES ($user_id, $balance)";
    if ($conn->query($create_query) === TRUE) {
        echo '<script>alert("Wallet Created successfully!");</script>';
        header('Location: user_createwallet.php');
        exit();
    } else {
        echo "Error creating wallet: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Wallet</title>
    <!-- Add your CSS stylesheets here -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Create Your Wallet, <?php echo $_SESSION['username']; ?>!</h1>
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
        <section>
            <h2>Create Wallet</h2>
            <?php if ($wallet_exists): ?>
                <p>You already have a wallet with the following details:</p>
                <p>Wallet ID: <?php echo $wallet_id; ?></p>
                <p>Balance: $<?php echo $balance; ?></p>
                <p>Created At: <?php echo $created_at; ?></p>
                <form action="" method="POST">
                    <input type="submit" name="delete_wallet" value="Delete Wallet">
                </form>
            <?php else: ?>
                <p>You have not created a wallet yet.</p>
                <form action="" method="POST">
                    <label for="balance">Enter Initial Balance:</label>
                    <input type="number" id="balance" name="balance" required>
                    <input type="submit" name="create_wallet" value="Create Wallet">
                </form>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>Copyright © 2023 Futsal Range</p>
    </footer>
</body>
</html>
