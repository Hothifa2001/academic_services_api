<?php
include '../config.php';

header('Content-Type: application/json; charset=utf-8');

$sql = "SELECT id, username, full_name, status FROM admin";
$result = $conn->query($sql);

$admins = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $admins[] = $row;
    }

    echo json_encode([
        "status" => "success",
        "data" => $admins
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "No admins found."
    ]);
}

$conn->close();
?>


