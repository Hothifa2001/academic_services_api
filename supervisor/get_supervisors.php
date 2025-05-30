<?php
header('Content-Type: application/json');
include '../config.php';

$query = "SELECT supervisor.*, department.name AS department_name
          FROM supervisor
          INNER JOIN department ON supervisor.department_id = department.id";

$result = mysqli_query($conn, $query);

$supervisors = [];

while ($row = mysqli_fetch_assoc($result)) {
    $supervisors[] = $row;
}

echo json_encode([
    "status" => "success",
    "data" => $supervisors
]);
?>
