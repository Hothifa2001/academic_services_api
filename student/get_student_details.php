<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

$input      = json_decode(file_get_contents("php://input"), true);
$student_id = $input['student_id'] ?? null;

if (!$student_id) {
    echo json_encode(["status"=>"error","message"=>"الرقم غير موجود"]);
    exit;
}

$stmt = $conn->prepare("
    SELECT 
        c.name               AS course_name,
        COUNT(a.student_id)  AS absence_count,      -- <<== هنا
        g.attendance_grade,
        g.midterm_grade,
        g.assignment_grade,
        g.total
    FROM course c
    LEFT JOIN attendance a 
      ON c.id = a.course_id 
     AND a.student_id = ? 
     AND a.is_present = 0
    LEFT JOIN grade g 
      ON c.id = g.course_id 
     AND g.student_id = ?
    WHERE 
      c.major_id = (SELECT major_id FROM student WHERE id = ?)
      AND c.level_id = (SELECT level_id  FROM student WHERE id = ?)
      AND c.term_id  = (SELECT term_id   FROM student WHERE id = ?)
    GROUP BY c.id
");

$stmt->bind_param("iiiii",
    $student_id,
    $student_id,
    $student_id,
    $student_id,
    $student_id
);

$stmt->execute();
$result = $stmt->get_result();

$out = [];
while ($row = $result->fetch_assoc()) {
    $out[] = $row;
}

echo json_encode(["status"=>"success","data"=>$out], JSON_UNESCAPED_UNICODE);

$stmt->close();
$conn->close();
