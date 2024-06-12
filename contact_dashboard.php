<?php
include 'config.php';
session_start();

// Check if admin is logged in, if not, redirect to admin login page
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch contact submissions from the database
$submissions_query = "SELECT * FROM ContactSubmissions";
$submissions_result = $conn->query($submissions_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Contact Submissions</title>
    <link rel="stylesheet" href="dashboard_style.css">
</head>
<body>
    <header>
        <h1>Admin Dashboard - Contact Submissions</h1>
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
            <h2>Contact Submissions</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Submission Date</th>
                </tr>
                <?php
                if ($submissions_result->num_rows > 0) {
                    while ($row = $submissions_result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["name"] . "</td>";
                        echo "<td>" . $row["email"] . "</td>";
                        echo "<td>" . $row["message"] . "</td>";
                        echo "<td>" . $row["submission_date"] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No contact submissions found</td></tr>";
                }
                ?>
            </table>
        </section>
    </main>
</body>
</html>
