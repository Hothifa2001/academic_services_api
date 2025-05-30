<?php
header('Content-Type: application/json');
include '../config.php';

$input = json_decode(file_get_contents('php://input'), true);
$students = $input['students'] ?? [];

foreach ($students as $s) {
    // استخراج المتغيرات طبقًا لتنسيق toJson
    $academicId = isset($s['Academic_ID']) ? (int) $s['Academic_ID'] : 0;
    $password   = isset($s['password'])     ? $s['password']         : '';
    $fullName   = isset($s['full_name'])    ? $s['full_name']        : '';
    $majorId    = isset($s['major_id'])     ? (int) $s['major_id']   : 0;
    $levelId    = isset($s['level_id'])     ? (int) $s['level_id']   : 0;
    $termId     = isset($s['term_id'])      ? (int) $s['term_id']    : 0;
    $status     = isset($s['status'])       ? (int) $s['status']     : 0;

    // تحضير البيان
    $stmt = $conn->prepare("
        INSERT INTO student
          (Academic_ID, password, full_name, major_id, level_id, term_id, status)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    // bind_param مع المتغيرات المناسبة، وأنواع البيانات: i s s i i i i
    $stmt->bind_param(
      "issiiii",
      $academicId,
      $password,
      $fullName,
      $majorId,
      $levelId,
      $termId,
      $status
    );

    $stmt->execute();
    $stmt->close();
}

echo json_encode(['status' => 'success']);
