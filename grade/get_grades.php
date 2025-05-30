<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

// جلب القيم من GET
$course_id = isset($_GET['course_id']) ? $_GET['course_id'] : null;
$academic_year = isset($_GET['academic_year']) ? $_GET['academic_year'] : null;
$department_id = isset($_GET['department_id']) ? $_GET['department_id'] : null;

// التحقق من القيم الأساسية
if (!$course_id || !$academic_year) {
  echo json_encode(["status" => "error", "message" => "course_id أو academic_year غير موجود"]);
  exit;
}

// إعداد شرط WHERE
$whereClause = "c.id = $course_id";
if ($department_id) {
  $whereClause .= " AND m.department_id = $department_id";
}

// ✅ جلب بيانات المادة والمدرس مع فلترة القسم إذا وُجد
$course_sql = "
  SELECT 
    c.id,
    c.name AS course_name,
    c.major_id,
    c.level_id,
    c.term_id,
    m.name AS major_name,
    l.name AS level_name,
    t.name AS term_name,
    i.full_name AS instructor_name
  FROM course c
  LEFT JOIN major m ON c.major_id = m.id
  LEFT JOIN level l ON c.level_id = l.id
  LEFT JOIN term t ON c.term_id = t.id
  LEFT JOIN course_instructor ci ON ci.course_id = c.id AND ci.academic_year = '$academic_year'
  LEFT JOIN instructor i ON i.id = ci.instructor_id
  WHERE $whereClause
";

$course_result = $conn->query($course_sql);
$course_info = $course_result->fetch_assoc();

if (!$course_info) {
  echo json_encode(["status" => "error", "message" => "لم يتم العثور على المادة"]);
  exit;
}

// استخراج بيانات المادة
$major_id = $course_info['major_id'];
$level_id = $course_info['level_id'];
$term_id = $course_info['term_id'];
$course_name = $course_info['course_name'];
$major_name = $course_info['major_name'];
$level_name = $course_info['level_name'];
$term_name = $course_info['term_name'];
$instructor_name = $course_info['instructor_name'] ?? "غير محدد";

// ✅ جلب درجات الطلاب
$sql = "
  SELECT 
    s.id AS student_id,
    s.full_name,
    s.Academic_ID,
    IFNULL(g.attendance_grade, 0) AS attendance_grade,
    IFNULL(g.midterm_grade, 0) AS midterm_grade,
    IFNULL(g.assignment_grade, 0) AS assignment_grade,
    IFNULL(g.total, (IFNULL(g.attendance_grade,0) + IFNULL(g.midterm_grade,0) + IFNULL(g.assignment_grade,0))) AS total
  FROM student s
  LEFT JOIN grade g ON g.student_id = s.id AND g.course_id = ? AND g.academic_year = ?
  WHERE s.major_id = ? AND s.level_id = ? AND s.term_id = ? AND s.status = 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("isiii", $course_id, $academic_year, $major_id, $level_id, $term_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {
  $data[] = [
    "student_id" => $row["student_id"],
    "full_name" => $row["full_name"],
    "Academic_ID" => (int)$row["Academic_ID"],
    "major_name" => $major_name,
    "level_name" => $level_name,
    "term_name" => $term_name,
    "course_name" => $course_name,
    "instructor_name" => $instructor_name,
    "course_id" => $course_id,
    "academic_year" => $academic_year,
    "attendance_grade" => number_format((float)$row["attendance_grade"], 1),
    "midterm_grade" => number_format((float)$row["midterm_grade"], 1),
    "assignment_grade" => number_format((float)$row["assignment_grade"], 1),
    "total" => number_format((float)$row["total"], 1)
  ];
}

echo json_encode(["status" => "success", "data" => $data], JSON_UNESCAPED_UNICODE);

$stmt->close();
$conn->close();
?>
