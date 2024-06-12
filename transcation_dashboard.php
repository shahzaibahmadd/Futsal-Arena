<?php
require 'config.php';
session_start();

// Check if admin is logged in, if not, redirect to admin login page
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch transactions data
$sql = "SELECT * FROM Transactions";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions</title>
    <link rel="stylesheet" href="dashboard_style.css">
</head>
<body>
    <header>
        <h1>Transactions</h1>
        <nav>
            <ul>
                <li><button> <a href="dashboard.php">User and Booking </a></li>
                <li><button> <a href="coach_dashboard.php">Coaching</a></li>
                <li><button> <a href="trainee_dashboard.php">Trainees</a></button></li>
                <li><button> <a href="contact_dashboard.php">Customers info</a></button></li>
                <li><button> <a href="venue_dashboard.php">Venues</a></button></li>    
                <li><button> <a href="wallet_dashboard.php">User Wallets</a></button></li>                
                <li><button> <a href="admin_walletdashboard.php">My Wallet</a></button></li>                
                <li><button> <a href="transcation_dashboard.php">Transactions</a></button></li>                 
                <li><button> <a href="index.html">Logout</a></button></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>All Transactions</h2>
            <table>
                <tr>
                    <th>Transaction ID</th>
                    <th>User ID</th>
                    <th>Booking ID</th>
                    <th>Amount</th>
                    <th>Payment Method</th>
                    <th>Status</th>
                    <th>Timestamp</th>
                </tr>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["transaction_id"] . "</td>";
                        echo "<td>" . $row["user_id"] . "</td>";
                        echo "<td>" . $row["booking_id"] . "</td>";
                        echo "<td>" . $row["amount"] . "</td>";
                        echo "<td>" . $row["payment_method"] . "</td>";
                        echo "<td>" . $row["status"] . "</td>";
                        echo "<td>" . $row["timestamp"] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No transactions found</td></tr>";
                }
                ?>
            </table>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Futsal Range</p>
    </footer>
</body>
</html>

<?php
// Close database connection
$conn->close();
?>
