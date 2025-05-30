<?php
header('Content-Type: application/json');
include '../config.php';

$query = "SELECT student.*, major.name AS major_name, level.name AS level_name, term.name AS term_name
          FROM student
          INNER JOIN major ON student.major_id = major.id
          INNER JOIN level ON student.level_id = level.id
          INNER JOIN term ON student.term_id = term.id";

$result = mysqli_query($conn, $query);

$students = [];

while ($row = mysqli_fetch_assoc($result)) {
    $students[] = $row;
}

echo json_encode([
    "status" => "success",
    "data" => $students
]);
?>
