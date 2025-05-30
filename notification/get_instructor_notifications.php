<?php
include '../config.php';
header('Content-Type: application/json');

// التحقق من وجود instructor_id (اختياري)
$instructor_id = $_GET['instructor_id'] ?? null;

$query = "SELECT * FROM instructor_notification WHERE 1=1";
$params = [];
$types = "";

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
    $notifications[] = $row;
}

echo json_encode([
    'status' => 'success',
    'data' => $notifications  // المفتاح الموحد الذي تتوقعه Flutter
]);

$stmt->close();
$conn->close();
