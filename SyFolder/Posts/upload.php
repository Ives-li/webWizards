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


// Handle API requests when called through the requesting browser aka client
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        handleGetRequest();
        break;
    case 'POST':
        handlePostRequest();
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
        break;
}

function handleGetRequest()
{
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM posts'); // sql to get all items from items table, make sure this table exist first
    $stmt->execute();
    $posts = $stmt->fetchAll();
    echo json_encode($posts);
}

function handlePostRequest()
{
    global $pdo;
    $title = $_POST['title'];
    $content = $_POST['content'];
    $imageSrc = $_FILES['image']['name'];
    $uploadDirectory = 'uploads/';
    $targetPath = $uploadDirectory . $imageSrc;
    move_uploaded_file($_FILES['image']['tmp_name'], $targetPath);
    $stmt = $pdo->prepare('INSERT INTO posts (title, content, imageSrc, time) VALUES (?, ?, ?, NOW())');
    $stmt->execute([$title, $content, $imageSrc]);
    http_response_code(200);
    // author
}

