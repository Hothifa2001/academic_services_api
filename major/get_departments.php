<?php
header('Content-Type: application/json');
include '../config.php';

$query = "SELECT id, name FROM department ORDER BY id DESC";

$result = mysqli_query($conn, $query);

$departments = [];

while ($row = mysqli_fetch_assoc($result)) {
    $departments[] = $row;
}

echo json_encode([
    "status" => "success",
    "data" => $departments
]);
?>
