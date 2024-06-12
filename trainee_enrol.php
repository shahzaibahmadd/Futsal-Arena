<?php
require 'config.php';

// Fetch available coaching sessions
$sessions_query = "SELECT coaching_sessions.*, coaches.name AS coach_name FROM coaching_sessions LEFT JOIN coaches ON coaching_sessions.coach_id = coaches.id";
$sessions_result = $conn->query($sessions_query);

// Handle enrollment
if (isset($_POST['enroll_action'])) {
    $trainee_name = $_POST['name'];
    $trainee_mobile = $_POST['mobile'];
    $trainee_email = $_POST['email'];
    $session_id = $_POST['session_id'];

    // Check if trainee exists
    $trainee_query = "SELECT * FROM trainees WHERE email='$trainee_email'";
    $trainee_result = $conn->query($trainee_query);
    if ($trainee_result->num_rows > 0) {
        $trainee = $trainee_result->fetch_assoc();
        $trainee_id = $trainee['id'];
    } else {
        $conn->query("INSERT INTO trainees (name, mobile, email) VALUES ('$trainee_name', '$trainee_mobile', '$trainee_email')");
        $trainee_id = $conn->insert_id;
    }

    // Enroll trainee in session
    $conn->query("INSERT INTO enrollments (trainee_id, coaching_session_id) VALUES ('$trainee_id', '$session_id')");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Futsal Coaching</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Futsal Coaching Sessions</h1>
        <nav>
            <ul>
                <li><button><a href="index.html">Home</a></button></li>
                <li><button><a href="about.html">About</a></button></li>
                <li><button><a href="services.html">Services</a></button></li>
                <li><button><a href="contact.html">Contact</a></button></li>
                <li><button id="loginBtn">Login</button></li>
                <li><button id="registerBtn">Register</button></li>
                <li><button id="adminLoginBtn">Admin Login</button></li>
                <li><button><a href="trainee_enrol.php">Trainee Enrollment</a></button></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>Available Sessions</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Coach</th>
                    <th>Location</th>
                    <th>Date</th>
                    <th>Time Slot</th>
                    <th>Max Trainees</th>
                    <th>Enroll</th>
                </tr>
                <?php
                if ($sessions_result->num_rows > 0) {
                    while ($row = $sessions_result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["coach_name"] . "</td>";
                        echo "<td>" . $row["location"] . "</td>";
                        echo "<td>" . $row["date"] . "</td>";
                        echo "<td>" . $row["time_slot"] . "</td>";
                        echo "<td>" . $row["max_trainees"] . "</td>";
                        echo "<td>
                                <form action='' method='POST'>
                                    <input type='hidden' name='session_id' value='" . $row["id"] . "'>
                                    <input type='hidden' name='enroll_action' value='enroll'>
                                    <input type='text' name='name' placeholder='Name' required>
                                    <input type='text' name='mobile' placeholder='Mobile' required>
                                    <input type='email' name='email' placeholder='Email' required>
                                    <button type='submit'>Enroll</button>
                                </form>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No coaching sessions available</td></tr>";
                }
                ?>
            </table>
        </section>
        

    <!-- Login Modal -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Login</h2>
            <form action="login.php" method="post">
                <input type="text" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
        </div>
    </div>

    <!-- Register Modal -->
<div id="registerModal" class="modal">
    <div class="modal-content">
        <label for="register-toggle" class="close">&times;</label>
        <h2>Register</h2>
        <form action="register.php" method="post" id="registerForm">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="mobile" placeholder="Mobile Number" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit">Register</button>
        </form>
    </div>
</div>


<!-- Admin Login Modal -->
<div id="adminLoginModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Admin Login</h2>
        <form action="admin_login.php" method="post">
            <input type="email" name="admin_email" placeholder="Email" required>
            <input type="password" name="admin_password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</div>
    </main>
    <footer>
        <p>Copyright © 2023 Futsal Range</p>
    </footer>
</body>
<script src="Js/script.js"></script>
</html>
