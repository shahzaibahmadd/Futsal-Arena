<?php
session_start();
include 'config.php';

$error_message = '';
$success_message = '';

// Handle the booking form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $location = $_POST['location'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $field_number = $_POST['field_number'];
    $payment_method = $_POST['payment_method']; // Added payment method

    // Check for availability
    $sql = "SELECT * FROM bookings WHERE location = ? AND date = ? AND time_slot = ? AND field_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $location, $date, $time, $field_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error_message = "Sorry, the slot is not available.";
    } else {
        // Book the slot if available
        if ($payment_method == 'online') {
            // Assuming user_id and admin_user_id are defined somewhere in the configuration
            $user_id = $_SESSION['user_id'];
            $admin_user_id = 5; // Assuming admin's user_id is 1

            // Check user's balance
            $check_balance_sql = "SELECT balance FROM Wallet WHERE user_id = ?";
            $stmt_check_balance = $conn->prepare($check_balance_sql);
            $stmt_check_balance->bind_param("i", $user_id);
            $stmt_check_balance->execute();
            $result_check_balance = $stmt_check_balance->get_result();
            $row_check_balance = $result_check_balance->fetch_assoc();
            $user_balance = $row_check_balance['balance'];
            $stmt_check_balance->close();

            // Check if user has sufficient balance
            $required_amount = 10.00;
            if ($user_balance < $required_amount) {
                $error_message = "Insufficient balance. Please add funds to your wallet.";
            } else {
                // Start transaction
                $conn->begin_transaction();

                // Book the slot
                $sql = "INSERT INTO bookings (user_id, location, date, time_slot, field_number) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("isssi", $_SESSION['user_id'], $location, $date, $time, $field_number);
                if ($stmt->execute()) {
                    $booking_id = $stmt->insert_id; // Get the booking ID
                    $stmt->close();
                    $success_message = "Booking confirmed!";

                    // Deduct $10 from user wallet and add to admin wallet
                    $deduction_amount = 10.00;
                    $update_user_wallet_sql = "UPDATE Wallet SET balance = balance - ? WHERE user_id = ?";
                    $update_admin_wallet_sql = "UPDATE admin_wallet SET balance = balance + ? WHERE admin_user_id = ?";
                    $insert_transaction_sql = "INSERT INTO Transactions (user_id, booking_id, amount, payment_method, status) VALUES (?, ?, ?, ?, 'completed')";

                    // Deduct amount from user's wallet
                    $stmt_update_user_wallet = $conn->prepare($update_user_wallet_sql);
                    $stmt_update_user_wallet->bind_param("di", $deduction_amount, $user_id);
                    $stmt_update_user_wallet->execute();
                    $stmt_update_user_wallet->close();

                    // Add amount to admin's wallet
                    $stmt_update_admin_wallet = $conn->prepare($update_admin_wallet_sql);
                    $stmt_update_admin_wallet->bind_param("di", $deduction_amount, $admin_user_id);
                    $stmt_update_admin_wallet->execute();
                    $stmt_update_admin_wallet->close();
                    // Add amount to admin's wallet
// $stmt = $conn->prepare($update_admin_wallet_sql);
// $stmt->bind_param("di", $deduction_amount, $admin_user_id);
// if ($stmt->execute()) {
//     echo "Admin's wallet updated successfully."; // Debugging statement
// } else {
//     echo "Error updating admin's wallet: " . $stmt->error; // Error handling
// }
// $stmt->close();


                    // Insert transaction record
                    $stmt_insert_transaction = $conn->prepare($insert_transaction_sql);
                    $stmt_insert_transaction->bind_param("iids", $user_id, $booking_id, $deduction_amount, $payment_method);
                    $stmt_insert_transaction->execute();
                    $stmt_insert_transaction->close();

                    // Commit transaction
                    $conn->commit();
                } else {
                    $error_message = "Error in booking: " . $stmt->error;
                }
            }
        } else {
            // Book the slot without payment if payment method is cash
            $sql = "INSERT INTO bookings (user_id, location, date, time_slot, field_number) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isssi", $_SESSION['user_id'], $location, $date, $time, $field_number);
            if ($stmt->execute()) {
                $success_message = "Booking confirmed!";
            } else {
                $error_message = "Error in booking: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// Fetch existing bookings for today
$current_date = date('Y-m-d');
$locations = ['Wapda Town', 'Model Town', 'DHA'];
$bookings = [];
foreach ($locations as $loc) {
    $sql = "SELECT * FROM bookings WHERE location = ? AND date = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $loc, $current_date);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $bookings[$loc][] = $row;
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Now - Futsal Range</title>
    <link rel="stylesheet" href="booknow.css">
</head>
<body>
    <header>
        <h1>Futsal Range</h1>
        <nav>
            <ul>
                <li><a href="user_dashboard.php">Home</a></li>
                <li><a href="booknow.php">Book Now</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="user_history.php">Booking History</a></li>
                <li><a href="user_wallet.php">My wallet</a></li>
                <li><a href="user_createwallet.php">Create wallet</a></li>
                <li><a href="user_transaction.php">My transactions</a></li>
                <li><a href="user_submitreview.php">Submit Review</a></li>
                <li><a href="index.html">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <div class="booking-container">
            <h2>Book Indoor Cricket or Futsal Field</h2>
            <?php
            if (!empty($success_message)) {
                echo "<p class='success'>$success_message</p>";
            }
            if (!empty($error_message))
            {
                echo "<p class='error'>$error_message</p>";
            }
            ?>
            <div class="booking-form">
                <form action="booknow.php" method="post">
                    <label for="location">Select Location</label>
                    <select id="location" name="location" required>
                        <option value="">Choose a location</option>
                        <option value="Wapda Town">Wapda Town</option>
                        <option value="Model Town">Model Town</option>
                        <option value="DHA">DHA</option>
                    </select>

                    <label for="field_number">Select Field Number:</label>
                    <select name="field_number" id="field_number" required>
                        <option value="">Select Field Number</option>
                        <option value="1">Field No. 1</option>
                        <option value="2">Field No. 2</option>
                    </select>

                    <label for="date">Select Date</label>
                    <input type="date" id="date" name="date" required>

                    <label for="time">Select Time</label>
                    <select id="time" name="time" required>
                        <option value="">Choose a time slot</option>
                        <?php
                        for ($i = 0; $i < 24; $i++) {
                            $time_slot = sprintf("%02d:00-%02d:00", $i, $i + 1);
                            echo "<option value='$time_slot'>$time_slot</option>";
                        }
                        ?>
                    </select>

                    <!-- Payment method selection -->
                    <label for="payment_method">Select Payment Method:</label>
                    <select id="payment_method" name="payment_method" required>
                        <option value="">Choose payment method</option>
                        <option value="cash">Cash</option>
                        <option value="online">Online</option>
                    </select>

                    <button type="submit">Book Now!</button>
                </form>
            </div>
            <div class="availability-table">
                <h2>Available and Booked Slots for Today</h2>
                <?php foreach ($locations as $loc): ?>
                    <h3><?php echo $loc; ?></h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Time Slot</th>
                                <th>Field 1</th>
                                <th>Field 2</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            for ($i = 0; $i < 24; $i++) {
                                $time_slot = sprintf("%02d:00-%02d:00", $i, $i + 1);
                                echo "<tr>
                                        <td>$time_slot</td>";

                                for ($k = 1; $k <= 2; $k++) {
                                    $is_booked = false;
                                    if (isset($bookings[$loc])) {
                                        foreach ($bookings[$loc] as $booking) {
                                            if ($booking['date'] == $current_date && $booking['time_slot'] == $time_slot && $booking['field_number'] == $k) {
                                                $is_booked = true;
                                                break;
                                            }
                                        }
                                    }
                                    $class = $is_booked ? "booked" : "available";
                                    echo "<td class=\"$class\">" . ($is_booked ? "Booked" : "Available") . "</td>";
                                }

                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
    <footer>
        <p>&copy; 2023 Futsal Range</p>
    </footer>
</body>
</html>
