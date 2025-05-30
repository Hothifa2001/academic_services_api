<?php
// details.php
header('Content-Type: application/json; charset=utf-8');

// 1) قراءة الطلب
$input = json_decode(file_get_contents('php://input'), true);
if (empty($input['departmentName']) || !isset($input['itemName'])) {
    http_response_code(400);
    echo json_encode([
        'status'=>'error',
        'message'=>'departmentName and itemName are required'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 2) تعقيم المدخلات
$dept = preg_replace('/[^A-Za-z0-9_\-ا-ي ]/u','',$input['departmentName']);
$item = preg_replace('/[^A-Za-z0-9_\-ا-ي \.\-]/u','',$input['itemName']);
$path = '';
if (!empty($input['path'])) {
    $raw   = str_replace(['..\\','../'], '', $input['path']);
    $clean = preg_replace('/[^A-Za-z0-9_\-ا-ي\/ ]/u','',$raw);
    $path  = trim($clean, "/\\");
}

// 3) بناء المسار الكامل
$root      = realpath(dirname(__DIR__,2).'/library');
$baseDept  = $root . DIRECTORY_SEPARATOR . $dept;
$targetDir = $path === ''
    ? $baseDept
    : $baseDept . DIRECTORY_SEPARATOR . $path;
$target    = $targetDir . DIRECTORY_SEPARATOR . $item;

if (!file_exists($target)) {
    http_response_code(404);
    echo json_encode(['status'=>'error','message'=>'Item not found'], JSON_UNESCAPED_UNICODE);
    exit;
}

// 4) جمع التفاصيل
$isDir     = is_dir($target);
$size      = $isDir ? null : filesize($target);
$created   = date('Y-m-d H:i:s', filectime($target));
$modified  = date('Y-m-d H:i:s', filemtime($target));
$itemCount = null;
if ($isDir) {
    $children = array_diff(scandir($target), ['.','..']);
    $itemCount = count($children);
}

// 5) إعادة النتيجة
echo json_encode([
    'status' => 'success',
    'data'   => [
        'name'       => $item,
        'type'       => $isDir ? 'dir' : 'file',
        'size'       => $size,
        'created'    => $created,
        'modified'   => $modified,
        'itemCount'  => $itemCount,
        'path'       => $path,  // للمراجع
    ]
], JSON_UNESCAPED_UNICODE);
