<?php
header('Content-Type: application/json');
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(["status" => "error", "message" => "Only POST allowed"]);
    exit;
}

$title = $_POST['title'] ?? null;
$content = $_POST['content'] ?? null;
$imageBase64 = $_POST['image_base64'] ?? null;

if (!$title || !$imageBase64) {
    echo json_encode(["status" => "error", "message" => "Missing title or image"]);
    exit;
}

$query = "INSERT INTO advertisement (title, content, image_path) 
          VALUES ('$title', '$content', '$imageBase64')";

if (mysqli_query($conn, $query)) {
    echo json_encode(["status" => "success", "message" => "Ad created successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
}
?>
