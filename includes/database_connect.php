<?php
$conn = mysqli_connect(
    "bn9qtfihhqmjmmsizz7v-mysql.services.clever-cloud.com",
    "bn9qtfihhqmjmmsizz7v",
    "usy0m5lemjkk30lh",
    "yeYn6lShcCi9AjDI0d4S"
);

if (!$conn || mysqli_connect_errno()) {
    header('Content-Type: application/json');
    echo json_encode(["success" => false, "message" => "Failed to connect to MySQL. Please contact the admin."]);
    exit;
}
