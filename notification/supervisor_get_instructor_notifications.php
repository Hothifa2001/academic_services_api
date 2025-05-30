<?php
include '../config.php';
header('Content-Type: application/json');

// قراءة الفلاتر من GET
$department_id = $_GET['department_id'] ?? null;
$instructor_id = $_GET['instructor_id'] ?? null;

$query = "SELECT * FROM supervisor_to_instructor_notifications WHERE 1=1";
$params = [];
$types = "";

if (!empty($department_id)) {
    $query .= " AND department_id = ?";
    $types .= "i";
    $params[] = $department_id;
}

if (!empty($instructor_id)) {
    $query .= " AND instructor_id = ?";
    $types .= "i";
    $params[] = $instructor_id;
}

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
$row['target_is_student'] = 0; // ✅ لتحديد أن الإشعار موجه للمدرس
    $notifications[] = $row;
}

echo json_encode(['status' => 'success', 'data' => $notifications]);

$stmt->close();
$conn->close();
