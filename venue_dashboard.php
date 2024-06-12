<?php
require 'config.php';
session_start();

$error_message = '';

// Check if admin is logged in, if not, redirect to admin login page
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle CRUD operations for venues
if (isset($_POST['venue_action'])) {
    switch ($_POST['venue_action']) {
        case 'create':
            $name = $_POST['name'];
            $location = $_POST['location'];
            $description = $_POST['description'];
            $num_fields = $_POST['num_fields'];
            $sql = "INSERT INTO Venue (name, location, description, num_fields) VALUES ('$name', '$location', '$description', '$num_fields')";
            $conn->query($sql);
            break;
        case 'update':
            $id = $_POST['id'];
            $name = $_POST['name'];
            $location = $_POST['location'];
            $description = $_POST['description'];
            $num_fields = $_POST['num_fields'];
            $sql = "UPDATE Venue SET name='$name', location='$location', description='$description', num_fields='$num_fields' WHERE venue_id='$id'";
            $conn->query($sql);
            break;
        case 'delete':
            $id = $_POST['id'];
            $sql = "DELETE FROM Venue WHERE venue_id='$id'";
            $conn->query($sql);
            break;
    }
}

// Fetch venues data from the database
$venues_query = "SELECT * FROM Venue";
$venues_result = $conn->query($venues_query);
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
                <li><button> <a href="transcation_dashboard.php">Transactions</a></button></li>      
                <li><button> <a href="index.html">Logout</a></button></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>Venues</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Location</th>
                    <th>Description</th>
                    <th>Number of Fields</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
                <?php
                if ($venues_result->num_rows > 0) {
                    while ($row = $venues_result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["venue_id"] . "</td>";
                        echo "<td>" . $row["name"] . "</td>";
                        echo "<td>" . $row["location"] . "</td>";
                        echo "<td>" . $row["description"] . "</td>";
                        echo "<td>" . $row["num_fields"] . "</td>";
                        echo "<td>" . $row["created_at"] . "</td>";
                        echo "<td>
                                <form action='' method='POST' style='display:inline;'>
                                    <input type='hidden' name='id' value='" . $row["venue_id"] . "'>
                                    <input type='hidden' name='venue_action' value='delete'>
                                    <button type='submit'>Delete</button>
                                </form>
                                <button onclick=\"populateVenueForm('" . $row["venue_id"] . "', '" . $row["name"] . "', '" . $row["location"] . "', '" . $row["description"] . "', '" . $row["num_fields"] . "')\">Update</button>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No venues found</td></tr>";
                }
                ?>
            </table>

            <h3>Add / Update Venue</h3>
            <form action="" method="POST">
                <input type="hidden" name="venue_action" value="create" id="venue_action">
                <input type="hidden" name="id" id="venue_id">
                <input type="text" name="name" placeholder="Name" id="venue_name" required>
                <input type="text" name="location" placeholder="Location" id="venue_location" required>
                <textarea name="description" placeholder="Description" id="venue_description" required></textarea>
                <input type="number" name="num_fields" placeholder="Number of Fields" id="venue_num_fields" required>
                <button type="submit">Submit</button>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Futsal Range</p>
    </footer>

    <script>
        function populateVenueForm(id, name, location, description, num_fields) {
            document.getElementById('venue_id').value = id;
            document.getElementById('venue_name').value = name;
            document.getElementById('venue_location').value = location;
            document.getElementById('venue_description').value = description;
            document.getElementById('venue_num_fields').value = num_fields;
            document.getElementById('venue_action').value = 'update';
        }
    </script>
</body>
</html>

<?php
// Close database connection
$conn->close();
?>
