<?php
require 'config.php';
session_start();

// Check if admin is logged in, if not, redirect to admin login page
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Function to check for existing sessions or bookings
function checkConflicts($conn, $coach_id, $location, $date, $time_slot, $field_no, $session_id = null) {
    // Check for existing coaching sessions at the same time for the same coach
    $coach_conflict_query = "SELECT * FROM coaching_sessions 
                             WHERE coach_id='$coach_id' 
                             AND date='$date' 
                             AND time_slot='$time_slot'";
    if ($session_id) {
        $coach_conflict_query .= " AND id != '$session_id'";
    }
    $coach_conflict_result = $conn->query($coach_conflict_query);

    if ($coach_conflict_result->num_rows > 0) {
        return "This coach is already scheduled for a session at this date and time slot.";
    }

    // Check for existing coaching sessions at the same location, field, date, and time slot
    $session_conflict_query = "SELECT * FROM coaching_sessions 
                               WHERE location='$location' 
                               AND date='$date' 
                               AND time_slot='$time_slot' 
                               AND field_no='$field_no'";
    if ($session_id) {
        $session_conflict_query .= " AND id != '$session_id'";
    }
    $session_conflict_result = $conn->query($session_conflict_query);

    if ($session_conflict_result->num_rows > 0) {
        return "A coaching session is already scheduled at this location, field, date, and time slot.";
    }

    // Check for existing user bookings
    $booking_conflict_query = "SELECT * FROM bookings 
                               WHERE location='$location' 
                               AND date='$date' 
                               AND time_slot='$time_slot' 
                               AND field_number='$field_no'";
    $booking_conflict_result = $conn->query($booking_conflict_query);

    if ($booking_conflict_result->num_rows > 0) {
        return "The field is already booked by a user at this location, date, and time slot.";
    }

    return null;
}

// Handle CRUD operations for coaches
if (isset($_POST['coach_action'])) {
    switch ($_POST['coach_action']) {
        case 'create':
            $name = $_POST['name'];
            $mobile = $_POST['mobile'];
            $email = $_POST['email'];
            $experience = $_POST['experience'];
            $specialties = $_POST['specialties'];

            $sql = "INSERT INTO coaches (name, mobile, email, experience, specialties) 
                    VALUES ('$name', '$mobile', '$email', '$experience', '$specialties')";
            $conn->query($sql);
            break;

        case 'update':
            $id = $_POST['id'];
            $name = $_POST['name'];
            $mobile = $_POST['mobile'];
            $email = $_POST['email'];
            $experience = $_POST['experience'];
            $specialties = $_POST['specialties'];

            $sql = "UPDATE coaches 
                    SET name='$name', mobile='$mobile', email='$email', experience='$experience', specialties='$specialties' 
                    WHERE id='$id'";
            $conn->query($sql);
            break;

        case 'delete':
            $id = $_POST['id'];
            $sql = "DELETE FROM coaches WHERE id='$id'";
            $conn->query($sql);
            break;
    }
}

// Handle CRUD operations for coaching sessions
if (isset($_POST['session_action'])) {
    switch ($_POST['session_action']) {
        case 'create':
            $coach_id = $_POST['coach_id'];
            $location = $_POST['location'];
            $date = $_POST['date'];
            $time_slot = $_POST['time_slot'];
            $max_trainees = $_POST['max_trainees'];
            $field_no = $_POST['field_no'];

            // Check for conflicts
            $conflict_message = checkConflicts($conn, $coach_id, $location, $date, $time_slot, $field_no);
            if ($conflict_message) {
                echo "<script>alert('$conflict_message');</script>";
                break;
            }

            $sql = "INSERT INTO coaching_sessions (coach_id, location, date, time_slot, max_trainees, field_no) 
                    VALUES ('$coach_id', '$location', '$date', '$time_slot', '$max_trainees', '$field_no')";
            $conn->query($sql);
            break;

        case 'update':
            $id = $_POST['id'];
            $coach_id = $_POST['coach_id'];
            $location = $_POST['location'];
            $date = $_POST['date'];
            $time_slot = $_POST['time_slot'];
            $max_trainees = $_POST['max_trainees'];
            $field_no = $_POST['field_no'];

            // Check for conflicts
            $conflict_message = checkConflicts($conn, $coach_id, $location, $date, $time_slot, $field_no, $id);
            if ($conflict_message) {
                echo "<script>alert('$conflict_message');</script>";
                break;
            }

            $sql = "UPDATE coaching_sessions 
                    SET coach_id='$coach_id', location='$location', date='$date', time_slot='$time_slot', max_trainees='$max_trainees', field_no='$field_no' 
                    WHERE id='$id'";
            $conn->query($sql);
            break;

        case 'delete':
            $id = $_POST['id'];
            $sql = "DELETE FROM coaching_sessions WHERE id='$id'";
            $conn->query($sql);
            break;
    }
}

// Fetch coaches data from the database and store in an array
$coaches_query = "SELECT * FROM coaches";
$coaches_result = $conn->query($coaches_query);
$coaches = [];
if ($coaches_result->num_rows > 0) {
    while ($row = $coaches_result->fetch_assoc()) {
        $coaches[] = $row;
    }
}

// Fetch sessions data from the database
$sessions_query = "SELECT coaching_sessions.*, coaches.name AS coach_name 
                   FROM coaching_sessions 
                   LEFT JOIN coaches ON coaching_sessions.coach_id = coaches.id";
$sessions_result = $conn->query($sessions_query);

// Define available locations and time slots
$locations = ["Wapda Town", "Model Town", "DHA"]; // Example locations
$time_slots = [
    "06:00-07:00", "07:00-08:00", "08:00-09:00", "09:00-10:00", 
    "10:00-11:00", "11:00-12:00", "12:00-13:00", "13:00-14:00", 
    "14:00-15:00", "15:00-16:00", "16:00-17:00", "17:00-18:00", 
    "18:00-19:00", "19:00-20:00", "20:00-21:00", "21:00-22:00",
    "22:00-23:00", "23:00-00:00", "00:00-01:00", "01:00-02:00",
    "02:00-03:00", "03:00-04:00", "04:00-05:00", "05:00-06:00"
];

$fields = range(1, 2); // Example field numbers
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Coaching</title>
    <link rel="stylesheet" href="dashboard_style.css">
</head>
<body>
    <header>
        <h1>Admin Dashboard - Coaching</h1>
        <nav>
            <ul>
                <li><button><a href="dashboard.php">User and Booking</a></li>
                <li><button><a href="coach_dashboard.php">Coaching</a></li>
                <li><button><a href="trainee_dashboard.php">Trainees</a></button></li>
                <li><button><a href="contact_dashboard.php">Customers info</a></button></li>
                <li><button><a href="venue_dashboard.php">Venues</a></button></li>
                <li><button><a href="wallet_dashboard.php">User Wallets</a></button></li>
                <li><button><a href="admin_walletdashboard.php">My Wallet</a></button></li>
                <li><button><a href="transcation_dashboard.php">Transactions</a></button></li>
                <li><button><a href="index.html">Logout</a></button></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>Coaches</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Mobile</th>
                    <th>Email</th>
                    <th>Experience</th>
                    <th>Specialties</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($coaches as $coach) { ?>
                    <tr>
                        <td><?php echo $coach['id']; ?></td>
                        <td><?php echo $coach['name']; ?></td>
                        <td><?php echo $coach['mobile']; ?></td>
                        <td><?php echo $coach['email']; ?></td>
                        <td><?php echo $coach['experience']; ?></td>
                        <td><?php echo $coach['specialties']; ?></td>
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $coach['id']; ?>">
                                <button type="submit" name="coach_action" value="delete">Delete</button>
                            </form>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $coach['id']; ?>">
                                <input type="hidden" name="name" value="<?php echo $coach['name']; ?>">
                                <input type="hidden" name="mobile" value="<?php echo $coach['mobile']; ?>">
                                <input type="hidden" name="email" value="<?php echo $coach['email']; ?>">
                                <input type="hidden" name="experience" value="<?php echo $coach['experience']; ?>">
                                <input type="hidden" name="specialties" value="<?php echo $coach['specialties']; ?>">
                                <button type="submit" name="coach_action" value="update">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </section>

        <section>
            <h2>Add New Coach</h2>
            <form method="post">
                <input type="hidden" name="coach_action" value="create">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
                <label for="mobile">Mobile:</label>
                <input type="text" id="mobile" name="mobile" required>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                <label for="experience">Experience:</label>
                <input type="text" id="experience" name="experience" required>
                <label for="specialties">Specialties:</label>
                <input type="text" id="specialties" name="specialties" required>
                <button type="submit">Add Coach</button>
            </form>
        </section>

        <section>
            <h2>Coaching Sessions</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Coach Name</th>
                    <th>Location</th>
                    <th>Date</th>
                    <th>Time Slot</th>
                    <th>Max Trainees</th>
                    <th>Field No</th>
                    <th>Actions</th>
                </tr>
                <?php while ($session = $sessions_result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $session['id']; ?></td>
                        <td><?php echo $session['coach_name']; ?></td>
                        <td><?php echo $session['location']; ?></td>
                        <td><?php echo $session['date']; ?></td>
                        <td><?php echo $session['time_slot']; ?></td>
                        <td><?php echo $session['max_trainees']; ?></td>
                        <td><?php echo $session['field_no']; ?></td>
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $session['id']; ?>">
                                <button type="submit" name="session_action" value="delete">Delete</button>
                            </form>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $session['id']; ?>">
                                <input type="hidden" name="coach_id" value="<?php echo $session['coach_id']; ?>">
                                <input type="hidden" name="location" value="<?php echo $session['location']; ?>">
                                <input type="hidden" name="date" value="<?php echo $session['date']; ?>">
                                <input type="hidden" name="time_slot" value="<?php echo $session['time_slot']; ?>">
                                <input type="hidden" name="max_trainees" value="<?php echo $session['max_trainees']; ?>">
                                <input type="hidden" name="field_no" value="<?php echo $session['field_no']; ?>">
                                <button type="submit" name="session_action" value="update">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </section>

        <section>
            <h2>Add New Coaching Session</h2>
            <form method="post">
                <input type="hidden" name="session_action" value="create">
                <label for="coach_id">Coach:</label>
                <select id="coach_id" name="coach_id" required>
                    <?php foreach ($coaches as $coach) { ?>
                        <option value="<?php echo $coach['id']; ?>"><?php echo $coach['name']; ?></option>
                    <?php } ?>
                </select>
                <label for="location">Location:</label>
                <select id="location" name="location" required>
                    <?php foreach ($locations as $location) { ?>
                        <option value="<?php echo $location; ?>"><?php echo $location; ?></option>
                    <?php } ?>
                </select>
                <label for="date">Date:</label>
                <input type="date" id="date" name="date" required>
                <label for="time_slot">Time Slot:</label>
                <select id="time_slot" name="time_slot" required>
                    <?php foreach ($time_slots as $time_slot) { ?>
                        <option value="<?php echo $time_slot; ?>"><?php echo $time_slot; ?></option>
                    <?php } ?>
                </select>
                <label for="max_trainees">Max Trainees:</label>
                <input type="number" id="max_trainees" name="max_trainees" required>
                <label for="field_no">Field No:</label>
                <select id="field_no" name="field_no" required>
                    <?php foreach ($fields as $field_no) { ?>
                        <option value="<?php echo $field_no; ?>"><?php echo $field_no; ?></option>
                    <?php } ?>
                </select>
                <button type="submit">Add Session</button>
            </form>
        </section>
    </main>
</body>
</html>