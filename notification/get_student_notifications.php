<?php
include '../config.php';
header('Content-Type: application/json');

// جلب بيانات التصنيف
$department_id = $_GET['department_id'] ?? null;
$major_id      = $_GET['major_id'] ?? null;
$level_id      = $_GET['level_id'] ?? null;
$course_id     = $_GET['course_id'] ?? null;

$query = "SELECT * FROM student_notification WHERE 1=1";
$params = [];
$types = "";

// إضافة الشروط حسب البيانات المُرسلة
if (!empty($department_id)) {
    $query .= " AND department_id = ?";
    $types .= "i";
    $params[] = $department_id;
}
if (!empty($major_id)) {
    $query .= " AND major_id = ?";
    $types .= "i";
    $params[] = $major_id;
}
if (!empty($level_id)) {
    $query .= " AND level_id = ?";
    $types .= "i";
    $params[] = $level_id;
}
if (!empty($course_id)) {
    $query .= " AND course_id = ?";
    $types .= "i";
    $params[] = $course_id;
}

// تجهيز الاستعلام
$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

// ✅ المفتاح الموحد
echo json_encode([
    'status' => 'success',
    'data' => $notifications
]);

$stmt->close();
$conn->close();
