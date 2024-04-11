<?php

// Database configuration
$host = 'localhost';
$dbName = 'webWizards'; // create this DB first
$username = 'root';
$password = '';

$dsn = "mysql:host=$host;dbname=$dbName;";

try {
    $pdo = new PDO($dsn, $username, $password);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}


$function = $_GET['function'];

//check the passed function and run the corresponding
if ($function === 'fetchProfile') {
    fetchProfile();
    //$response = array('result' => 'success');
    //echo json_encode($response);
} else if ($function === 'saveContent') {
    saveContent();
} else {
    $response = array('error' => 'Invalid function');
    echo json_encode($response);
}

function fetchProfile()
{
    global $pdo;
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['user_id'];
    $stmt = $pdo->prepare('SELECT * FROM users WHERE user_id = ?');
    $stmt->execute([$id]);
    $stmt->execute();
    $response = $stmt->fetchAll();
    echo json_encode($response);
    http_response_code(200);
}

function saveContent()
{
    global $pdo;
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['user_id'];
    $content = $data['content'];
    $stmt = $pdo->prepare('UPDATE users SET profile_content=? WHERE user_id=?');
    $stmt->execute([$content, $id]);
    http_response_code(200);
}

