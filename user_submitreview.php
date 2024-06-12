<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$error_message = '';
$success_message = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $venue_id = $_POST['venue_id'];
    $coaching_session_id = $_POST['coaching_session_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    // Insert review into Reviews table
    $sql = "INSERT INTO Reviews (user_id, venue_id, coaching_session_id, rating, comment) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiids", $user_id, $venue_id, $coaching_session_id, $rating, $comment);
    if ($stmt->execute()) {
        $success_message = "Review submitted successfully!";
    } else {
        $error_message = "Error submitting review: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Review</title>
    <link rel="stylesheet" href="style.css"> <!-- Assuming you have a CSS file for styling -->
</head>
<body>
    <header>
        <h1>Submit Review</h1>
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
        </nav>
    </header>
    <main>
    <div class="container review-form">
    <h2>Submit Your Review</h2>
    <?php
    if (!empty($success_message)) {
        echo "<p class='success'>$success_message</p>";
    }
    if (!empty($error_message)) {
        echo "<p class='error'>$error_message</p>";
    }
    ?>
    <form action="" method="post"> <!-- Removed action attribute to submit to the same page -->
        <input type="hidden" name="venue_id" value="1"> <!-- Replace with actual venue ID -->
        <input type="hidden" name="coaching_session_id" value="1"> <!-- Replace with actual coaching session ID -->
        <label for="rating">Rating:</label>
        <input type="number" id="rating" name="rating" min="1" max="5" required>

        <label for="comment">Comment:</label>
        <textarea id="comment" name="comment" rows="4" required></textarea>

        <button type="submit">Submit Review</button>
    </form>
</div>

    </main>
    <footer>
    <p>Copyright © 2023 Futsal Range</p>
    </footer>
</body>
</html>
