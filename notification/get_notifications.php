<?php
include '../config.php';

$query = "SELECT * FROM notification ORDER BY created_at DESC";
$result = $conn->query($query);

$notifications = [];

while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

echo json_encode(["status" => "success", "data" => $notifications]);
?>