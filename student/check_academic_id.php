<?php
header('Content-Type: application/json');
include '../config.php';

// قبول academic_id من POST أو GET
if (isset($_POST['academic_id'])) {
    $academic_id = $_POST['academic_id'];
} elseif (isset($_GET['academic_id'])) {
    $academic_id = $_GET['academic_id'];
} else {
    echo json_encode(['status' => 'error', 'message' => 'الرقم الأكاديمي مطلوب']);
    exit();
}

// === التعديل هنا: استخدام اسم الجدول الصحيح `student` ===
$stmt = $conn->prepare(
    "SELECT COUNT(*) AS count 
     FROM `student` 
     WHERE `Academic_ID` = ?"
);
$stmt->bind_param("s", $academic_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$exists = $row['count'] > 0;
echo json_encode(['status' => 'success', 'exists' => $exists]);

$stmt->close();
$conn->close();
