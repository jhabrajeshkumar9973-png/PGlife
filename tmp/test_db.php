<?php
$conn = @mysqli_connect("127.0.0.1", "root", "", "pglife");
if ($conn) {
    echo "DB_OK\n";
    mysqli_close($conn);
} else {
    echo "DB_FAIL: " . mysqli_connect_error() . "\n";
}
