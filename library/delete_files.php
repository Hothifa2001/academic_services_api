<?php
// delete_files.php

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (empty($_POST['departmentName']) || !isset($_POST['files'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'departmentName and files[] are required'], JSON_UNESCAPED_UNICODE);
    exit;
}

$deptName = preg_replace('/[^A-Za-z0-9_\-ا-ي ]/u', '', $_POST['departmentName']);
$files = $_POST['files'];
$path = '';
if (!empty($_POST['path'])) {
    $cleanPath = preg_replace('/[^A-Za-z0-9_\-ا-ي\/ ]/u', '', $_POST['path']);
    $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, trim($cleanPath, "/\\"));
}

$root = realpath(dirname(__DIR__, 2) . '/library');
$baseDept = $root . DIRECTORY_SEPARATOR . $deptName;
$targetDir = $path === '' ? $baseDept : $baseDept . DIRECTORY_SEPARATOR . $path;

if (!is_dir($targetDir)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Target folder not found'], JSON_UNESCAPED_UNICODE);
    exit;
}

$deleted = [];
foreach ($files as $file) {
    $safeName = preg_replace('/[^A-Za-z0-9_\-ا-ي\.\ ]/u', '', $file);
    $fullPath = $targetDir . DIRECTORY_SEPARATOR . $safeName;
    if (is_file($fullPath) && unlink($fullPath)) {
        $deleted[] = $safeName;
    }
}

echo json_encode([
    'status' => count($deleted) ? 'success' : 'error',
    'deleted' => $deleted,
    'message' => count($deleted) ? 'Files deleted' : 'No files deleted'
], JSON_UNESCAPED_UNICODE);