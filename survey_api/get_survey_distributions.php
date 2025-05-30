<?php
header('Content-Type: application/json');
include_once '../config.php';

// استقبل survey_id كـ GET parameter (اختياري)
$survey_id = isset($_GET['survey_id']) ? intval($_GET['survey_id']) : null;

// بناء جملة WHERE إذا تم تمرير survey_id
$where = $survey_id
    ? "WHERE sd.survey_id = $survey_id"
    : "";

$sql = "
  SELECT
    sd.id,
    sd.survey_id,
    s.title        AS survey_title,
    sd.course_id,
    c.name         AS course_name,
    sd.academic_year
  FROM survey_distribution sd
  JOIN survey s  ON sd.survey_id = s.id
  JOIN course c  ON sd.course_id = c.id
  $where
  ORDER BY sd.academic_year DESC, s.title, c.name
";

$result = $conn->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode([
      'status'  => 'error',
      'message' => 'خطأ في الاستعلام: ' . $conn->error
    ]);
    exit;
}

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
      'id'             => (int)$row['id'],
      'survey_id'      => (int)$row['survey_id'],
      'survey_title'   => $row['survey_title'],
      'course_id'      => (int)$row['course_id'],
      'course_name'    => $row['course_name'],
      'academic_year'  => $row['academic_year'],
    ];
}

if (empty($data)) {
    echo json_encode([
      'status'  => 'error',
      'message' => 'لا توجد توزيعات للاستبيان'
    ]);
} else {
    echo json_encode([
      'status' => 'success',
      'data'   => $data
    ]);
}
