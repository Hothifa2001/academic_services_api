<?php
include '../config.php';

$data = json_decode(file_get_contents('php://input'), true);

$id = $data['id'];

$query = "DELETE FROM notification WHERE id=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}
?>