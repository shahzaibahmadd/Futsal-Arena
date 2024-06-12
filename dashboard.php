<?php
require 'config.php';
session_start();

$error_message = '';

// Check if admin is logged in, if not, redirect to admin login page
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle CRUD operations for users
if (isset($_POST['user_action'])) {
    switch ($_POST['user_action']) {
        case 'create':
            $name = $_POST['name'];
            $mobile = $_POST['mobile'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $sql = "INSERT INTO users (name, mobile, email, password) VALUES ('$name', '$mobile', '$email', '$password')";
            $conn->query($sql);
            break;
        case 'update':
            $id = $_POST['id'];
            $name = $_POST['name'];
            $mobile = $_POST['mobile'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $sql = "UPDATE users SET name='$name', mobile='$mobile', email='$email', password='$password' WHERE id='$id'";
            $conn->query($sql);
            break;
        case 'delete':
            $id = $_POST['id'];
            $sql = "DELETE FROM users WHERE id='$id'";
            if ($conn->query($sql) === TRUE) {
                // Reset AUTO_INCREMENT
                $result = $conn->query("SELECT MAX(id) AS max_id FROM users");
                $row = $result->fetch_assoc();
                $max_id = $row['max_id'];
        
                // Set AUTO_INCREMENT to max_id + 1, or to 1 if no rows are left
                $next_id = $max_id ? $max_id + 1 : 1;
                $conn->query("ALTER TABLE users AUTO_INCREMENT = $next_id");
            }    
         break;
    }
}

// Handle CRUD operations for bookings
if (isset($_POST['booking_action'])) {
    switch ($_POST['booking_action']) {
        case 'create':
            $user_id = $_POST['user_id'];
            $location = $_POST['location'];
            $date = $_POST['date'];
            $time_slot = $_POST['time_slot'];
            $field_number = $_POST['field_number'];

            // Check for availability
            $availability_sql = "SELECT * FROM bookings WHERE location = '$location' AND date = '$date' AND time_slot = '$time_slot' AND field_number = '$field_number'";
            $availability_result = $conn->query($availability_sql);
            if ($availability_result->num_rows > 0) {
                $error_message = "Sorry, the slot is not available.";
            } else {
                // Proceed to create booking if slot is available
                $sql = "INSERT INTO bookings (user_id, location, date, time_slot, field_number) VALUES ('$user_id', '$location', '$date', '$time_slot', '$field_number')";
                $conn->query($sql);
            }
            break;

        case 'update':
            $id = $_POST['id'];
            $user_id = $_POST['user_id'];
            $location = $_POST['location'];
            $date = $_POST['date'];
            $time_slot = $_POST['time_slot'];
            $field_number = $_POST['field_number'];
            $sql = "UPDATE bookings SET user_id='$user_id', location='$location', date='$date', time_slot='$time_slot', field_number='$field_number' WHERE id='$id'";
            $conn->query($sql);
            break;
        case 'delete':
            $id = $_POST['id'];
            $sql = "DELETE FROM bookings WHERE id='$id'";
            $conn->query($sql);
            break;
    }
}

// Fetch users and bookings data from the database
$users_query = "SELECT * FROM users";
$users_result = $conn->query($users_query);

$bookings_query = "SELECT bookings.*, users.name AS user_name FROM bookings LEFT JOIN users ON bookings.user_id = users.id";
$bookings_result = $conn->query($bookings_query);
?>

<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="dashboard_style.css">
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
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
            <h2>Users</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Mobile</th>
                    <th>Email</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
                <?php
                if ($users_result->num_rows > 0) {
                    while ($row = $users_result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["name"] . "</td>";
                        echo "<td>" . $row["mobile"] . "</td>";
                        echo "<td>" . $row["email"] . "</td>";
                        echo "<td>" . $row["created_at"] . "</td>";
                        echo "<td>
                                <form action='' method='POST' style='display:inline;'>
                                    <input type='hidden' name='id' value='" . $row["id"] . "'>
                                    <input type='hidden' name='user_action' value='delete'>
                                    <button type='submit'>Delete</button>
                                </form>
                                <button onclick=\"populateUserForm('" . $row["id"] . "', '" . $row["name"] . "', '" . $row["mobile"] . "', '" . $row["email"] . "')\">Update</button>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No users found</td></tr>";
                }
                ?>
            </table>

            <h3>Add / Update User</h3>
            <form action="" method="POST">
                <input type="hidden" name="user_action" value="create" id="user_action">
                <input type="hidden" name="id" id="user_id">
                <input type="text" name="name" placeholder="Name" id="user_name" required>
                <input type="text" name="mobile" placeholder="Mobile" id="user_mobile" required>
                <input type="email" name="email" placeholder="Email" id="user_email" required>
                <input type="password" name="password" placeholder="Password" id="user_password" required>
                <button type="submit">Submit</button>
            </form>
        </section>
        
        <section>
            <h2>Bookings</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Location</th>
                    <th>Date</th>
                    <th>Time Slot</th>
                    <th>Field Number</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
                <?php
                if ($bookings_result->num_rows > 0) {
                    while ($row = $bookings_result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["user_name"] . "</td>";
                        echo "<td>" . $row["location"] . "</td>";
                        echo "<td>" . $row["date"] . "</td>";
                        echo "<td>" . $row["time_slot"] . "</td>";
                        echo "<td>" . $row["field_number"] . "</td>";
                        echo "<td>" . $row["created_at"] . "</td>";
                        echo "<td>
                                <form action='' method='POST' style='display:inline;'>
                                    <input type='hidden' name='id' value='" . $row["id"] . "'>
                                    <input type='hidden' name='booking_action' value='delete'>
                                    <button type='submit'>Delete</button>
                                </form>
                                <button onclick=\"populateBookingForm('" . $row["id"] . "', '" . $row["user_id"] . "', '" . $row["location"] . "', '" . $row["date"] . "', '" . $row["time_slot"] . "', '" . $row["field_number"] . "')\">Update</button>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No bookings found</td></tr>";
                }
                ?>
            </table>

            <h3>Add / Update Booking</h3>
            <section id="messages">
        <?php
        if (!empty($error_message)) {
            echo "<div class='error-message'>$error_message</div>";
        }
        ?>
    </section>
            <form action="" method="POST">
                <input type="hidden" name="booking_action" value="create" id="booking_action">
                <input type="hidden" name="id" id="booking_id">
                <input type="text" name="user_id" placeholder="User ID" id="booking_user_id" required>
                
                <!-- Locations dropdown -->
                <label for="booking_location">Location</label>
                <select name="location" id="booking_location" required>
                    <option value="">Select Location</option>
                    <option value="Wapda Town">Wapda Town</option>
                    <option value="Model Town">Model Town</option>
                    <option value="DHA">DHA</option>
                    <!-- Add more locations as needed -->
                </select>

                <!-- Field Number dropdown -->
                <label for="booking_field_number">Select Field Number:</label>
                <select name="field_number" id="booking_field_number" required>
                    <option value="">Select Field Number</option>
                    <option value="1">Field No. 1</option>
                    <option value="2">Field No. 2</option>
                    <!-- Add more options as needed -->
                </select>

                <label for="booking_date">Date</label>
                <input type="date" name="date" id="booking_date" required>
                
                <label for="booking_time_slot">Time Slot</label>
                <select name="time_slot" id="booking_time_slot" required>
                    <option value="">Select Time Slot</option>
                    <?php
                    for ($i = 0; $i < 24; $i++) {
                        $start_time = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
                        $end_time = str_pad(($i + 1) % 24, 2, '0', STR_PAD_LEFT) . ':00';
                        echo "<option value='$start_time-$end_time'>$start_time - $end_time</option>";
                    }
                    ?>
                </select>
                
                <button type="submit">Submit</button>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Futsal Range</p>
    </footer>

    <script>
        function populateUserForm(id, name, mobile, email) {
            document.getElementById('user_id').value = id;
            document.getElementById('user_name').value = name;
            document.getElementById('user_mobile').value = mobile;
            document.getElementById('user_email').value = email;
            document.getElementById('user_action').value = 'update';
        }

        function populateBookingForm(id, user_id, location, date, time_slot, field_number) {
            document.getElementById('booking_id').value = id;
            document.getElementById('booking_user_id').value = user_id;
            document.getElementById('booking_location').value = location;
            document.getElementById('booking_date').value = date;
            document.getElementById('booking_time_slot').value = time_slot;
            document.getElementById('booking_field_number').value = field_number;
            document.getElementById('booking_action').value = 'update';
        }
    </script>
</body>
</html>

<?php
// Close database connection
$conn->close();
?>

