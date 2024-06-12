<?php
require 'config.php';
session_start();

// Check if admin is logged in, if not, redirect to admin login page
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch admin wallet information
$sql = "SELECT * FROM admin_wallet WHERE admin_user_id = {$_SESSION['admin_id']}";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

// Handle adding balance
if (isset($_POST['add_balance'])) {
    $amount = $_POST['amount'];
    // Update admin wallet balance
    $sql = "UPDATE admin_wallet SET balance = balance + $amount WHERE admin_user_id = {$_SESSION['admin_id']}";
    $conn->query($sql);
    // Set success message
    $_SESSION['success_message'] = "Balance added successfully";
    // Refresh the page
    header("Location: admin_walletdashboard.php");
    exit();
}

// Handle removing balance
if (isset($_POST['remove_balance'])) {
    $amount = $_POST['amount'];
    // Check if sufficient balance is available
    if ($row['balance'] >= $amount) {
        // Update admin wallet balance
        $sql = "UPDATE admin_wallet SET balance = balance - $amount WHERE admin_user_id = {$_SESSION['admin_id']}";
        $conn->query($sql);
        // Set success message
        $_SESSION['success_message'] = "Balance removed successfully";
        // Refresh the page
        header("Location: admin_walletdashboard.php");
        exit();
    } else {
        $error_message = "Insufficient balance";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Wallet</title>
    <link rel="stylesheet" href="dashboard_style.css">
</head>
<body>
    <header>
        <h1>Admin Wallet</h1>
        <nav>
            <ul>
                <li><button> <a href="dashboard.php">User and Booking </a></li>
                <li><button> <a href="coach_dashboard.php">Coaching</a></li>
                <li><button> <a href="trainee_dashboard.php">Trainees</a></button></li>
                <li><button> <a href="contact_dashboard.php">Customers info</a></button></li>
                <li><button> <a href="venue_dashboard.php">Venues</a></button></li>    
                <li><button> <a href="wallet_dashboard.php">User Wallets</a></button></li>  
                <li><button> <a href="admin_walletdashboard.php">My Wallet</a></button></li>                
                <li><button> <a href="index.html">Logout</a></button></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>Admin Wallet Information</h2>
            <?php if ($result->num_rows > 0) : ?>
                <table>
                    <tr>
                        <th>Wallet ID</th>
                        <th>Admin User ID</th>
                        <th>Balance</th>
                        <th>Created At</th>
                    </tr>
                    <tr>
                        <td><?php echo $row['wallet_id']; ?></td>
                        <td><?php echo $row['admin_user_id']; ?></td>
                        <td><?php echo $row['balance']; ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                    </tr>
                </table>
            <?php else : ?>
                <p>No wallet information found for the admin.</p>
            <?php endif; ?>
        </section>

        <section>
            <h2>Add Balance</h2>
            <form  method="post">
                <input type="number" name="amount" placeholder="Amount" required>
                <button type="submit" name="add_balance">Add Balance</button>
            </form>
        </section>

        <section>
            <h2>Remove Balance</h2>
            <?php if (isset($error_message)) : ?>
                <p><?php echo $error_message; ?></p>
            <?php endif; ?>
            <form  method="post">
                <input type="number" name="amount" placeholder="Amount" required>
                <button type="submit" name="remove_balance">Remove Balance</button>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Futsal Range</p>
    </footer>

    <?php
    // Display success message if set
    if (isset($_SESSION['success_message'])) {
        echo "<script>alert('{$_SESSION['success_message']}');</script>";
        unset($_SESSION['success_message']); // Clear the session variable
    }
    ?>

</body>
</html>

<?php
// Close database connection
$conn->close();
?>
