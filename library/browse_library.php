<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$basePath = realpath("../../library");
$subPath = isset($_POST['path']) ? $_POST['path'] : "";

// تنظيف المسار
$cleanPath = trim($subPath, '/');
$targetPath = realpath($basePath . '/' . $cleanPath);

// إذا كان المسار غير صحيح، استخدم المسار الأساسي
if ($targetPath === false || strpos($targetPath, $basePath) !== 0) {
    $targetPath = $basePath;
}

$items = scandir($targetPath);
$response = [];

foreach ($items as $item) {
    if ($item === '.' || $item === '..') continue;

    $fullPath = $targetPath . '/' . $item;
    $isDir = is_dir($fullPath);
    $size = $isDir ? null : filesize($fullPath);
    
    // إرجاع اسم العنصر فقط بدون المسار الكامل
    $response[] = [
        "name" => $item,
        "type" => $isDir ? "folder" : "file",
        "size" => $size,
        "path" => $cleanPath // إرجاع المسار الحالي فقط
    ];
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);