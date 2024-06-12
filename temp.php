document.addEventListener("DOMContentLoaded", function() {
    // Get the modal elements
    var loginModal = document.getElementById('loginModal');
    var registerModal = document.getElementById('registerModal');
    var adminLoginModal = document.getElementById('adminLoginModal');  // Adding the admin login modal

    // Get the buttons that open the modals
    var loginBtn = document.getElementById('loginBtn');
    var registerBtn = document.getElementById('registerBtn');
    var adminLoginBtn = document.getElementById('adminLoginBtn');  // Adding the admin login button

    // Get the <span> elements that close the modals
    var spans = document.getElementsByClassName("close");

    // Function to open a specific modal
    function openModal(modal) {
        modal.style.display = "block";
    }

    // Function to close a specific modal
    function closeModal(modal) {
        modal.style.display = "none";
    }

    // Event listeners for opening modals
    loginBtn.onclick = function() {
        openModal(loginModal);
    };
    registerBtn.onclick = function() {
        openModal(registerModal);
    };
    adminLoginBtn.onclick = function() {
        openModal(adminLoginModal);  // Handling click event for admin login
    };

    // Event listeners for closing modals using the <span> elements
    for (let i = 0; i < spans.length; i++) {
        spans[i].onclick = function() {
            closeModal(loginModal);
            closeModal(registerModal);
            closeModal(adminLoginModal);  // Closing the admin login modal
        };
    }

    // Event listener to close modals if the user clicks outside of the modal content area
    window.onclick = function(event) {
        if (event.target == loginModal) {
            closeModal(loginModal);
        } else if (event.target == registerModal) {
            closeModal(registerModal);
        } else if (event.target == adminLoginModal) {  // Handling outside click for admin login modal
            closeModal(adminLoginModal);
        }
    };

    // Preventing modal from closing if the click is inside the modal content
    document.querySelectorAll('.modal-content').forEach(item => {
        item.addEventListener('click', function(event) {
            event.stopPropagation(); // Prevents click inside the modal from reaching the window
        });
    });

    // Add event listener for form submission to handle AJAX if needed
    document.getElementById("registerForm").onsubmit = function(event) {
        event.preventDefault(); // Stop the form from submitting via HTTP

        var formData = new FormData(this);

        fetch('register.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            alert("user created"); // Display success message or error from server
            closeModal(registerModal); // Close modal on successful registration
        })
        .catch(error => console.error('Error:', error));
    };
});




































<?php
require 'config.php';
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // var_dump($_POST);  // Debugging output

    // Extracting data from the POST array
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Optional: Validate the data (e.g., check if passwords match)
    if ($password !== $confirm_password) {
        die('Passwords do not match.');
    }

    // Hashing the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // SQL statement to insert a new record
    $sql = "INSERT INTO users (name, email, mobile, password) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die('MySQL prepare error: ' . $conn->error);
    }

    // Binding parameters and executing the statement
    $stmt->bind_param("ssss", $name, $email, $mobile, $hashed_password);
    if ($stmt->execute()) {
        echo "User registered successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Closing the statement and connection
    $stmt->close();
    $conn->close();
}
?>
