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
        $_SESSION["pid"] = $_POST["pid"];
        //update database of the joined author
    } else if ($_POST['type'] === 'updateDb'){
        $stmt = $pdo->prepare('INSERT INTO posts (people) VALUES (?) WHERE id = :pid');
        $stmt->bindParam(':pid', $_SESSION["pid"]);
        $stmt->execute([$_SESSION['uname']]);   
        http_response_code(200);
    }

}



