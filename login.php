<?php
session_start(); 

// Handle API requests when called through the requesting browser aka client
switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        login();
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
        break;
}
function login() { 
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

    $uname = $_POST['username'];
    $pw = $_POST['password'];

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
            $_SESSION['uname'] = $uname; 
            header("Location: SyFolder/Homepage/homepage.html");
        } else {
            // Passwords do not match
            $response = "Invalid Username or Password";
            echo json_encode($response);
        }
    } else {
        // Username not found in the database
        $response = "Invalid Username or Password";
        echo json_encode($response);
    }
    $conn->close();
}
