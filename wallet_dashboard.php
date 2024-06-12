<?php
require 'config.php';
session_start();

$error_message = '';

// Check if admin is logged in, if not, redirect to admin login page
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle CRUD operations for wallets
if (isset($_POST['wallet_action'])) {
    switch ($_POST['wallet_action']) {
        case 'create':
            $user_id = $_POST['user_id'];
            $balance = $_POST['balance'];
            $sql = "INSERT INTO Wallet (user_id, balance) VALUES ('$user_id', '$balance')";
            $conn->query($sql);
            break;
        case 'add':
            $wallet_id = $_POST['wallet_id'];
            $amount = $_POST['amount'];
            $sql = "UPDATE Wallet SET balance = balance + $amount WHERE wallet_id='$wallet_id'";
            $conn->query($sql);
            break;
        case 'remove':
            $wallet_id = $_POST['wallet_id'];
            $amount = $_POST['amount'];
            $sql = "UPDATE Wallet SET balance = balance - $amount WHERE wallet_id='$wallet_id'";
            $conn->query($sql);
            break;
        case 'delete':
            $wallet_id = $_POST['wallet_id'];
            $sql = "DELETE FROM Wallet WHERE wallet_id='$wallet_id'";
            $conn->query($sql);
            break;
    }
}

// Fetch users data from the database along with their wallet information
$users_query = "SELECT users.id, users.name, Wallet.* FROM users LEFT JOIN Wallet ON users.id = Wallet.user_id";
$users_result = $conn->query($users_query);
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
                <li><button><a href="dashboard.php">User and Booking</a></button></li>
                <li><button><a href="coach_dashboard.php">Coaching</a></button></li>
                <li><button><a href="trainee_dashboard.php">Trainees</a></button></li>
                <li><button><a href="contact_dashboard.php">Customers info</a></button></li>
                <li><button><a href="venue_dashboard.php">Venues</a></button></li>
                <li><button><a href="wallet_dashboard.php">User Wallets</a></button></li>
                <li><button> <a href="admin_walletdashboard.php">My Wallet</a></button></li>   
                <li><button> <a href="transcation_dashboard.php">Transactions</a></button></li>      
                <li><button><a href="index.html">Logout</a></button></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>User Wallets</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>User ID</th>
                    <th>Balance</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
                <?php
                if ($users_result->num_rows > 0) {
                    while ($row = $users_result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["wallet_id"] . "</td>";
                        echo "<td>" . $row["name"] . "</td>";
                        echo "<td>" . $row["user_id"] . "</td>";
                        echo "<td>$" . $row["balance"] . "</td>";
                        echo "<td>" . $row["created_at"] . "</td>";
                        echo "<td>";
                        if ($row["wallet_id"]) {
                            echo "<form action='' method='POST' style='display:inline;'>
                                    <input type='hidden' name='wallet_id' value='" . $row["wallet_id"] . "'>
                                    <input type='hidden' name='wallet_action' value='add'>
                                    <input type='number' name='amount' placeholder='Add Amount' required>
                                    <button type='submit'>Add Money</button>
                                </form>
                                <form action='' method='POST' style='display:inline;'>
                                    <input type='hidden' name='wallet_id' value='" . $row["wallet_id"] . "'>
                                    <input type='hidden' name='wallet_action' value='remove'>
                                    <input type='number' name='amount' placeholder='Remove Amount' required>
                                    <button type='submit'>Remove Money</button>
                                </form>";
                        } else {
                            echo "<form action='' method='POST' style='display:inline;'>
                                    <input type='hidden' name='user_id' value='" . $row["id"] . "'>
                                    <input type='hidden' name='wallet_action' value='create'>
                                    <input type='number' name='balance' placeholder='Initial Balance' required>
                                    <button type='submit'>Create Wallet</button>
                                </form>";
                        }
                        if ($row["wallet_id"]) {
                            echo "<form action='' method='POST' style='display:inline;'>
                                    <input type='hidden' name='wallet_id' value='" . $row["wallet_id"] . "'>
                                    <input type='hidden' name='wallet_action' value='delete'>
                                    <button type='submit'>Delete Wallet</button>
                                </form>";
                        }
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No users found</td></tr>";
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
