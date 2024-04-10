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

// Query to retrieve posts by John Doe

$sql = "SELECT * FROM posts WHERE author = 'John Doe' ORDER BY time DESC";
$result = $conn->query($sql);

$posts = array();
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    $posts[] = $row;
  }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($posts);
?>