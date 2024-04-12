<?php

// Database configuration
$host = 'localhost';
$dbName = 'webWizards';
$username = 'root';
$password = '';
$conn = new mysqli($host, $username, $password, $dbName);

$function = $_GET['function'];

//check the passed function and run the corresponding
if ($function === 'fetchUsers') {
    fetchUsers();
} else if ($function === 'sendMsg') {
    sendMsg();
} else if ($function === 'fetchMsg'){
    fetchMsg();
} else {
    $response = array('error' => 'Invalid function');
    echo json_encode($response);
}

function fetchUsers()
{
    session_start();

    global $conn;
    //fetch the userdata
    $sql = "SELECT user_id, username FROM users";
    $result = $conn->query($sql);

    $userData = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $userData[] = array($row['user_id'], $row['username']);
        }
    }

    //filter out user himself
    foreach ($userData as $user) {
        if ($user[0] != $_SESSION['user_id']) {
            $filteredUsers[] = array($user[0], $user[1]);
        }
    }

    usort($filteredUsers, function ($i, $j) {
        return strcmp($i[1], $j[1]);
    });

    $conn->close();

    header('Content-Type: application/json');
    echo json_encode(array('users' => $filteredUsers));
}

function sendMsg()
{
    global $conn;

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    $message_content = $data['message_content'];
    $user_id = $data['user_id'];
    $receiver_id = $data['receiver_id'];

    //get the largest message_id and increment it by one for the new id
    $sql = "SELECT message_id FROM messages ORDER BY message_id DESC LIMIT 1";
    $message_id = mysqli_query($conn, $sql);
    if ($message_id) {
        $latestRecord = mysqli_fetch_assoc($message_id);
        $message_id = intval($latestRecord['message_id']);
        $message_id++;
    } else {
        $message_id = 1;
    }

    $sql = "INSERT INTO messages (message_id, message_content, sender_id, receiver_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('isii', $message_id, $message_content, $user_id, $receiver_id);

    if ($stmt->execute()) {
        echo json_encode("Message saved to the database.");
    } else {
        echo json_encode("Error: Failed to save the message.");
    }
}
function fetchMsg()
{
    global $conn;


    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    $user_id = $data['user_id'];
    $receiver_id = $data['receiver_id'];

    //join messages and users to get the username for response
    $stmt = $conn->prepare("SELECT m.message_id, m.message_content, s.username AS sender_username, r.username AS receiver_username
                            FROM messages AS m
                            JOIN users AS s ON m.sender_id = s.user_id
                            JOIN users AS r ON m.receiver_id = r.user_id
                            WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?)
                            ORDER BY m.message_id ASC");
                            
                            //javascript after calling this function, display the response inside a div with id=chatBox, the format should be user_id
    $stmt->bind_param('iiii', $receiver_id, $user_id, $user_id, $receiver_id);

    $stmt->execute();

    $result = $stmt->get_result();
    $messages = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();

    header('Content-Type: application/json');
    echo json_encode(['messages' => $messages]);
}
