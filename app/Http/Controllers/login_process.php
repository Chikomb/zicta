<?php
// Start the session (if not already started)
session_start();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Perform your authentication logic here, e.g., check against the database
    // Replace 'YOUR_USERNAME' and 'YOUR_PASSWORD' with the actual credentials to check
    $valid_username = 'YOUR_USERNAME';
    $valid_password = 'YOUR_PASSWORD';

    if ($username === $valid_username && $password === $valid_password) {
        // Authentication successful, set the session variable to mark the user as logged in
        $_SESSION['logged_in'] = true;
        
        // Redirect to the main USSD page or dashboard
        header('Location: ussd_service.php');
        exit;
    } else {
        // Authentication failed, display an error message or redirect back to the login page
        echo "Invalid username or password. Please try again.";
    }
}
?>
