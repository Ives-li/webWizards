<?php
if (array_key_exists('login', $_POST)) {
    $mail = $_POST['email'];
    $pw = $_POST['password'];
    login($mail, $pw);
}
function login($mail, $pw)
{
    // Database configuration
    $host = 'localhost';
    $dbName = 'webWizards';
    $username = 'root';
    $password = '';

    $conn = new mysqli($host, $username, $password, $dbName);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Escape the email to prevent SQL injection
    $email = $conn->real_escape_string($mail);

    $sql = "SELECT password,user_id FROM users WHERE email = '{$email}'";
    $result = $conn->query($sql);

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $storedPassword = $row['password'];
        $user_id= $row['user_id'];
        echo "{$storedPassword}";
        // Verify the inputted password against the fetched password
        if ($pw == $storedPassword) {
            // Start the session
            session_start();
            // Store the user_id in a session variable
            $_SESSION['user_id'] = $user_id;
            // Close the database connection
            header("Location: SyFolder/Homepage/homepage.html");
        } else {
            // Passwords do not match
            echo "Invalid email or password.";
        }
    } else {
        // Email not found in the database
        //echo "Invalid email or password.";
        echo "email not found.";
    }
    $conn->close();
}
