<?php
session_start();

// Check if the user_id and uname session variables are set
if (isset($_SESSION['user_id']) && isset($_SESSION['uname'])) {
    $user_id = $_SESSION['user_id'];
    $uname = $_SESSION['uname'];
    echo json_encode(['user_id' => $user_id, 'uname' => $uname]);
} else {
    echo json_encode(['user_id' => null, 'uname' => null]);
}