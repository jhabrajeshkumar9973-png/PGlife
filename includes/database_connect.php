<?php
$conn = mysqli_connect("127.0.0.1", "root", "", "pglife");

if (!$conn || mysqli_connect_errno()) {
    header('Content-Type: application/json');
    echo json_encode(["success" => false, "message" => "Failed to connect to MySQL. Please contact the admin."]);
    exit;
}
