<?php
include '../config.php';

$target_is_student = $_GET['target_is_student']; // 1 للطلاب، 0 للمدرسين

$query = "SELECT * FROM notification WHERE target_is_student=? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $target_is_student);
$stmt->execute();

$result = $stmt->get_result();
$notifications = [];

while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

echo json_encode(["status" => "success", "data" => $notifications]);
?>