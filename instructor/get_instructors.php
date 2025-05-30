<?php
header('Content-Type: application/json');
include '../config.php';

$query = "SELECT instructor.*, major.name AS major_name
          FROM instructor
          INNER JOIN major ON instructor.major_id = major.id";

$result = mysqli_query($conn, $query);

$instructors = [];

while ($row = mysqli_fetch_assoc($result)) {
    $instructors[] = $row;
}

echo json_encode([
    "status" => "success",
    "data" => $instructors
]);
?>
