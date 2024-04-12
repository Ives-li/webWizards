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
            // Store the user_id in a session variable
            $_SESSION['user_id'] = $user_id;
            $_SESSION['uname'] = $uname; 
            echo json_encode(['success' => true, 'user_id' => $user_id, 'uname' => $uname]);
        } else {    
            // Passwords do not match
            echo json_encode(['success' => false, 'error' => 'Invalid Username or Password']);
        }
    } else {
        // Username not found in the database
        echo json_encode(['success' => false, 'error' => 'Invalid Username or Password']);
    }
    $conn->close();
}
