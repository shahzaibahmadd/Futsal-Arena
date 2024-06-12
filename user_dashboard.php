<?php
// Start session and check if user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <!-- Add your CSS stylesheets here -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Welcome to Our Platform, <?php echo $_SESSION['username']; ?>!</h1>
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
            <h2>Futsal Arena</h2>
            <p>Welcome to our platform! We provide top-notch facilities for sports enthusiasts like you. Whether you're looking to book grounds for a game or join training sessions, we've got you covered.</p>
            <p>Explore our services and start your sports journey today!</p>
        </section>
        
        <!-- Add more sections with relevant content -->
    </main>

    <footer>
    <p>Copyright © 2023 Futsal Range</p>
    </footer>
</body>
</html>
