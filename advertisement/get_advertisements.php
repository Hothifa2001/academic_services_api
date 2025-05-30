<?php
header('Content-Type: application/json');
include '../config.php';

$query = "SELECT * FROM advertisement ORDER BY id DESC";
$result = mysqli_query($conn, $query);

$ads = [];

while ($row = mysqli_fetch_assoc($result)) {
    $ads[] = $row;
}

echo json_encode([
    "status" => "success",
    "data" => $ads
]);
?>
