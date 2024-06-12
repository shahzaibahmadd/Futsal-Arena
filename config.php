<?php
$host = 'localhost';  // Server where your MySQL database is hosted
$username = 'root';   // Username for the MySQL database
$password = '11221234';       // Password for the MySQL database
$dbname = 'futsal_db';  // Database name

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
// else {
//     echo "Successfully connected to the database.";
// }
?>
