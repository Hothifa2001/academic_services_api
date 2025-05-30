<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

$response = [];

try {
  $role = $_GET['role'] ?? '';
  $departmentId = $_GET['department_id'] ?? 0;

  // بيانات عامة للجميع
  $response['advertisements_count'] = $conn->query("SELECT COUNT(*) FROM advertisement")->fetch_row()[0];
  $response['surveys_count'] = $conn->query("SELECT COUNT(*) FROM survey")->fetch_row()[0];

  // إشعارات حسب الدور
  if ($role === 'supervisor') {
    $response['notifications_count'] = $conn->query("SELECT COUNT(*) FROM supervisor_to_instructor_notifications WHERE department_id = $departmentId")->fetch_row()[0]
                                      + $conn->query("SELECT COUNT(*) FROM supervisor_to_student_notifications WHERE department_id = $departmentId")->fetch_row()[0];

    // بيانات مخصصة للمشرف
    $response['supervised_students_count'] = $conn->query("SELECT COUNT(*) FROM student WHERE major_id IN (SELECT id FROM major WHERE department_id = $departmentId)")->fetch_row()[0];
    $response['supervised_instructors_count'] = $conn->query("SELECT COUNT(*) FROM instructor WHERE major_id IN (SELECT id FROM major WHERE department_id = $departmentId)")->fetch_row()[0];
    $response['supervised_majors_count'] = $conn->query("SELECT COUNT(*) FROM major WHERE department_id = $departmentId")->fetch_row()[0];
    $response['supervised_courses_count'] = $conn->query("SELECT COUNT(*) FROM course WHERE major_id IN (SELECT id FROM major WHERE department_id = $departmentId)")->fetch_row()[0];

  } elseif ($role === 'instructor') {
    $response['notifications_count'] = $conn->query("SELECT COUNT(*) FROM instructor_to_student_notifications")->fetch_row()[0];

  } else {
    // بيانات كاملة للمدير
    $response['students_count'] = $conn->query("SELECT COUNT(*) FROM student")->fetch_row()[0];
    $response['instructors_count'] = $conn->query("SELECT COUNT(*) FROM instructor")->fetch_row()[0];
    $response['departments_count'] = $conn->query("SELECT COUNT(*) FROM department")->fetch_row()[0];
    $response['majors_count'] = $conn->query("SELECT COUNT(*) FROM major")->fetch_row()[0];
    $response['courses_count'] = $conn->query("SELECT COUNT(*) FROM course")->fetch_row()[0];
    $response['notifications_count'] = $conn->query("SELECT COUNT(*) FROM supervisor_to_instructor_notifications")->fetch_row()[0]
                                      + $conn->query("SELECT COUNT(*) FROM supervisor_to_student_notifications")->fetch_row()[0];
  }

  echo json_encode(['status' => 'success', 'data' => $response], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
  echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
