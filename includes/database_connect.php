<?php
$conn = mysqli_connect(
    "bn9qtfihhqmjmmsizz7v-mysql.services.clever-cloud.com",
    "usy0m5lemjkk30lh",
    "_yeYn6lShcCi9AjD10d4S",
    "bn9qtfihhqmjmmsizz7v"
);

if (!$conn || mysqli_connect_errno()) {
    header('Content-Type: application/json');
    echo json_encode(["success" => false, "message" => "Failed to connect to MySQL. Please contact the admin."]);
    exit;
}