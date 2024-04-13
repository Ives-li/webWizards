<?php
// 连接到数据库
$host = "localhost";
$username = "root";
$password = "";
$dbname = "webWizards";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Query to retrieve posts by current user
session_start();
$uname = $_SESSION['uname'];
$sql = "SELECT id, title, content, imageSrc, date, activity FROM posts WHERE author = ? ORDER BY STR_TO_DATE(date, '%Y-%m-%d') DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $uname);
$stmt->execute();
$result = $stmt->get_result();

$posts = array();
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    $posts[] = $row;
  }
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($posts);
?>