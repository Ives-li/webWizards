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
    session_start(); 
    global $pdo;
    $title = $_POST['title'];
    $content = $_POST['content'];
    $activity = $_POST['activity'];
    $peopleNum = $_POST['people'];
    $date = $_POST['date'];
    $fromTime = $_POST['fromTime'];
    $toTime = $_POST['toTime'];


     switch ($activity) {
        case 'Hiking':
            $imageSrc = 'hiking.jpeg';
            break;
        case 'Badminton':
            $imageSrc = 'badminton.jpeg';
            break;
        case 'Swimming':
            $imageSrc = 'swimming.jpeg';
            break;
        case 'Football':
            $imageSrc = 'football.jpeg';
            break;
        default:
            $imageSrc = '';
    }

    // $imageSrc = $_FILES['image']['name'];
    // $uploadDirectory = 'uploads/';
    // $targetPath = $uploadDirectory . $imageSrc;
    // move_uploaded_file($_FILES['image']['tmp_name'], $targetPath); 
    $stmt = $pdo->prepare('INSERT INTO posts (title, content, activity, imageSrc, date, fromTime, toTime, author, people, peopleNum) VALUES (?, ?, ?, ?, ?, ?, ?,?,?,?)');
    $stmt->execute([$title, $content, $activity, $imageSrc, $date, $fromTime,$toTime, $_SESSION['uname'], $_SESSION['uname'],$peopleNum]);    
}

