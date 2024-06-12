<?php
require 'config.php';
session_start();

// Check if admin is logged in, if not, redirect to admin login page
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle CRUD operations for trainees
if (isset($_POST['trainee_action'])) {
    switch ($_POST['trainee_action']) {
        case 'create':
            $name = $_POST['name'];
            $mobile = $_POST['mobile'];
            $email = $_POST['email'];
            $sql = "INSERT INTO trainees (name, mobile, email) VALUES ('$name', '$mobile', '$email')";
            $conn->query($sql);
            break;
        case 'update':
            $id = $_POST['id'];
            $name = $_POST['name'];
            $mobile = $_POST['mobile'];
            $email = $_POST['email'];
            $sql = "UPDATE trainees SET name='$name', mobile='$mobile', email='$email' WHERE id='$id'";
            $conn->query($sql);
            break;
        case 'delete':
            $id = $_POST['id'];
            $sql = "DELETE FROM trainees WHERE id='$id'";
            $conn->query($sql);
            break;
    }
}

// Fetch trainees data from the database
$trainees_query = "SELECT * FROM trainees";
$trainees_result = $conn->query($trainees_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Trainees</title>
    <link rel="stylesheet" href="dashboard_style.css">
</head>
<body>
    <header>
        <h1>Admin Dashboard - Trainees</h1>
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
            <h2>Trainees</h2>
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
                if ($trainees_result->num_rows > 0) {
                    while ($row = $trainees_result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["name"] . "</td>";
                        echo "<td>" . $row["mobile"] . "</td>";
                        echo "<td>" . $row["email"] . "</td>";
                        echo "<td>" . $row["created_at"] . "</td>";
                        echo "<td>
                                <form action='' method='POST' style='display:inline;'>
                                    <input type='hidden' name='id' value='" . $row["id"] . "'>
                                    <input type='hidden' name='trainee_action' value='delete'>
                                    <button type='submit'>Delete</button>
                                </form>
                                <button onclick=\"populateTraineeForm('" . $row["id"] . "', '" . $row["name"] . "', '" . $row["mobile"] . "', '" . $row["email"] . "')\">Update</button>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No trainees found</td></tr>";
                }
                ?>
            </table>

            <h3>Add / Update Trainee</h3>
            <form action="" method="POST">
                <input type="hidden" name="trainee_action" value="create" id="trainee_action">
                <input type="hidden" name="id" id="trainee_id">
                <input type="text" name="name" placeholder="Name" id="trainee_name" required>
                <input type="text" name="mobile" placeholder="Mobile" id="trainee_mobile" required>
                <input type="email" name="email" placeholder="Email" id="trainee_email" required>
                <button type="submit">Submit</button>
            </form>
        </section>
    </main>
    <footer>
        <p>&copy; 2024 Futsal Range</p>
    </footer>
    <script>
        function populateTraineeForm(id, name, mobile, email) {
            document.getElementById('trainee_id').value = id;
            document.getElementById('trainee_name').value = name;
            document.getElementById('trainee_mobile').value = mobile;
            document.getElementById('trainee_email').value = email;
            document.getElementById('trainee_action').value = 'update';
        }
    </script>
</body>
</html>
