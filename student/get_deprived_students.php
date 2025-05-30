<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

// حدّ الغياب المسموح به قبل الحرمان
$threshold = 4;

$query = "
    SELECT 
        s.id               AS student_id,
        s.full_name        AS student_name,
        s.status           AS student_status,
        m.id               AS major_id,
        m.name             AS major_name,
        m.department_id    AS department_id,
        l.name             AS level_name,
        t.name             AS term_name,
        c.id               AS course_id,
        c.name             AS course_name,
        COUNT(a.student_id) AS absence_count
    FROM attendance a
    JOIN student     s ON a.student_id = s.id
    JOIN course      c ON a.course_id  = c.id
    JOIN major       m ON s.major_id   = m.id
    JOIN level       l ON s.level_id   = l.id
    JOIN term        t ON s.term_id    = t.id
    WHERE a.is_present = 0
    GROUP BY s.id, c.id
    HAVING absence_count > {$threshold}
    ORDER BY s.full_name ASC
";

$result = $conn->query($query);
if (!$result) {
    echo json_encode([
        "status"  => "error",
        "message" => $conn->error
    ]);
    exit;
}

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode([
    "status" => "success",
    "data"   => $data
]);

$conn->close();
