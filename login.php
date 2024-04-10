<?php
session_start(); 

if(array_key_exists('login', $_POST)) { 
    $uname = $_POST['username'];
    $pw = $_POST['password'];
    login($uname,$pw); 
} 
function login($uname, $pw) { 
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

    $sql = "SELECT password, user_id FROM users WHERE username = '{$uname}'";
    $result = $conn->query($sql);

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $storedPassword = $row['password'];
        $user_id= $row['user_id'];
        // Verify the inputted password against the fetched password
        if ($pw == $storedPassword) {
            // Start the session
            session_start();
            // Store the user_id in a session variable
            $_SESSION['user_id'] = $user_id;
            // Passwords match, user is authenticated
            $_SESSION['uname'] = $uname; 
            header("Location: SyFolder/Homepage/homepage.html");
        } else {
            // Passwords do not match
            echo "Invalid user or password.";
        }
    } else {
        // Email not found in the database
        //echo "Invalid email or password.";
        echo "email not found.";
    }
    $conn->close();
}
