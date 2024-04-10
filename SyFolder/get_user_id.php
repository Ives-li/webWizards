<?php
session_start();

// Retrieve the user_id from the session
$user_id = $_SESSION['user_id'];
$response = array('user_id' => $user_id);
header('Content-Type: application/json');
echo json_encode($response);