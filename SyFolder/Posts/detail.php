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

session_start();
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare('SELECT * FROM posts WHERE id = :pid');
    // Bind the session variable to the SQL statement
    $stmt->bindParam(':pid', $_SESSION["pid"]);
    // Execute the query
    $stmt->execute();
    // Fetch the result as an associative array
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    // Encode the result as JSON and echo it
    echo json_encode($post);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['type'] === 'sendPid') {
        if (isset($_POST["pid"])) {
            $pid = intval($_POST["pid"]);
            $_SESSION["pid"] = $pid;
            // Update database of the joined author
            // You should perform the necessary database operations here
        } else {
            // Handle the case where pid is not set in the POST data
        }
    }
    if ($_POST['type'] === 'updateDb') {
        if (isset($_SESSION["pid"])) {
            // Fetch the current value of the 'people' column
            $stmt = $pdo->prepare('SELECT people FROM posts WHERE id = :pid');
            $stmt->bindParam(':pid', $_SESSION["pid"], PDO::PARAM_INT);
            $stmt->execute();
            $current_people = $stmt->fetchColumn();

            // Concatenate the new value with the existing one
            $new_people = $current_people . ', ' . $_SESSION['uname'];

            // Perform the INSERT statement
            $stmt = $pdo->prepare('UPDATE posts SET people = :people WHERE id = :pid');
            $stmt->bindParam(':pid', $_SESSION["pid"], PDO::PARAM_INT);
            $stmt->bindParam(':people', $new_people);
            $stmt->execute();
        }
    }
    if ($_POST['type'] === 'getUname') {
        if (isset($_SESSION['uname'])) {
            echo json_encode($_SESSION['uname']);
        }
    }
    if ($_POST['type'] === 'checkUser') {
        $stmt = $pdo->prepare('SELECT people FROM posts WHERE id = :pid');
        $stmt->bindParam(':pid', $_SESSION["pid"], PDO::PARAM_INT);
        $stmt->execute();
        $current_people = $stmt->fetchColumn();
        if (strpos($current_people, $_SESSION['uname']) !== false) {
            echo 'true';
        } else {
            echo 'false';
        }
    }   
    
}

