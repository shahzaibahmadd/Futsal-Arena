<?php
// Start session and check if user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

// Include your database configuration file here
require 'config.php';

// Fetch active bookings
$user_id = $_SESSION['user_id'];
$current_date = date('Y-m-d');
$active_bookings_query = "SELECT * FROM bookings WHERE user_id = $user_id AND date >= '$current_date'";
$active_bookings_result = $conn->query($active_bookings_query);

// Fetch past bookings
$past_bookings_query = "SELECT * FROM bookings WHERE user_id = $user_id AND date < '$current_date'";
$past_bookings_result = $conn->query($past_bookings_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking History</title>
    
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Booking History</h1>
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
            <h2>Active Bookings</h2>
            <table>
                <tr>
                    <th>Booking ID</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Location</th>
                    <th>Field Number</th>
                    <!-- Add more columns as needed -->
                </tr>
                <?php
                if ($active_bookings_result->num_rows > 0) {
                    while ($row = $active_bookings_result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["date"] . "</td>";
                        echo "<td>" . $row["time_slot"] . "</td>";
                        echo "<td>" . $row["location"] . "</td>";
                        echo "<td>" . $row["field_number"] . "</td>";
                        // Add more cells for additional booking details
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No active bookings found</td></tr>";
                }
                ?>
            </table>
        </section>

        <section>
            <h2>Past Bookings</h2>
            <table>
                <tr>
                    <th>Booking ID</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Location</th>
                    <th>Field Number</th>
                    <!-- Add more columns as needed -->
                </tr>
                <?php
                if ($past_bookings_result->num_rows > 0) {
                    while ($row = $past_bookings_result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["date"] . "</td>";
                        echo "<td>" . $row["time_slot"] . "</td>";
                        echo "<td>" . $row["location"] . "</td>";
                        echo "<td>" . $row["field_number"] . "</td>";
                        // Add more cells for additional booking details
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No past bookings found</td></tr>";
                }
                ?>
            </table>
        </section>
    </main>

    <footer>
    <p>Copyright © 2023 Futsal Range</p>
    </footer>
</body>
</html>
