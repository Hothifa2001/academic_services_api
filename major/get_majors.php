<?php
header('Content-Type: application/json');
include '../config.php';

$query = "SELECT major.*, department.name AS department_name 
          FROM major 
          INNER JOIN department ON major.department_id = department.id 
          ORDER BY major.id DESC";

$result = mysqli_query($conn, $query);

$majors = [];

while ($row = mysqli_fetch_assoc($result)) {
    $majors[] = $row;
}

echo json_encode([
    "status" => "success",
    "data" => $majors
]);
?>
