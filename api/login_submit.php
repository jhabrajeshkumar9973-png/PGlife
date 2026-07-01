<?php
session_start();
header('Content-Type: application/json');
require("../includes/database_connect.php");

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if ($email === '' || $password === '') {
    echo json_encode(["success" => false, "message" => "Email and password are required."]);
    exit;
}

$email = mysqli_real_escape_string($conn, $email);
$password_hash = password_hash($password, PASSWORD_DEFAULT);

$sql = "SELECT * FROM users WHERE email='$email' LIMIT 1";
$result = mysqli_query($conn, $sql);
if (!$result) {
    echo json_encode(["success" => false, "message" => "Something went wrong!"]);
    exit;
}

$row = mysqli_fetch_assoc($result);
if (!$row) {
    echo json_encode(["success" => false, "message" => "Login failed! Invalid email or password."]);
    exit;
}

$stored_password = $row['password'];
$password_ok = false;
if (password_verify($password, $stored_password)) {
    $password_ok = true;
} elseif (sha1($password) === $stored_password) {
    $password_ok = true;
    // Upgrade legacy SHA1 password on next login
    $new_hash = password_hash($password, PASSWORD_DEFAULT);
    mysqli_query($conn, "UPDATE users SET password='" . mysqli_real_escape_string($conn, $new_hash) . "' WHERE id=" . intval($row['id']));
}

if (!$password_ok) {
    echo json_encode(["success" => false, "message" => "Login failed! Invalid email or password."]);
    exit;
}

$_SESSION['user_id'] = $row['id'];
$_SESSION['full_name'] = $row['full_name'];
$_SESSION['email'] = $row['email'];

echo json_encode(["success" => true, "message" => "Login successful!"]);
mysqli_close($conn);
