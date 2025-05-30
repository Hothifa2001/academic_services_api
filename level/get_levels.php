<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

$sql = "SELECT * FROM level";
$result = $conn->query($sql);

$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode(["status" => "success", "data" => $data]);

$conn->close();
