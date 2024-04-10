<?php
// Handle API requests when called through the requesting browser aka client
switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        handlePostRequest();
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
        break;
}

function handlePostRequest(){
    $host = 'localhost';
    $dbName = 'webwizards'; // create this DB first
    $username = 'root';
    $password = '';
    $conn = new mysqli($host, $username, $password, $dbName);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $uname = $_POST['username'];
    $pw = $_POST['password'];
    $confirmpw = $_POST['confirmPassword'];

    if ($pw === $confirmpw) {

        //Get the largest user_id and increment it by one for the new id
        $sql = "SELECT user_id FROM users ORDER BY user_id DESC LIMIT 1";
        $id = mysqli_query($conn, $sql);
        if ($id) {
            $latestRecord = mysqli_fetch_assoc($id);
            $id = intval($latestRecord['user_id']);
            $id++;
        } else {
            $id = 1;
        }

        // Prepare the SQL query
        $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM users WHERE username = ?");
        $stmt->bind_param("s", $uname);
        $stmt->execute();
        $result = $stmt->get_result();

        // Get the count value
        $row = $result->fetch_assoc();
        $count = $row["count"];

        // Check if the account exists
        if ($count > 0) {
            $response = "Username Already Registered!";
            echo json_encode("Username Already Registered!");
            http_response_code(400);
        } else {
            $stmt = $conn->prepare("INSERT INTO users (user_id, username, password) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $id, $uname, $pw);
            if ($stmt->execute()) {
                $response = "Successfully Registered!";
                echo json_encode($response);
                http_response_code(200);
            } else {
                $response = "Error: " . $stmt->error;
                echo json_encode($response);
                http_response_code(400);
            }
        }
    } else {
        $response = "Password not the same!";
        echo json_encode($response);
        http_response_code(400);
    }

    // Close the database connection
    $conn->close();

    // Set the response content type to JSON
    header('Content-Type: application/json');
}