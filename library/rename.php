<?php
// rename.php
header('Content-Type: application/json; charset=utf-8');

$input = json_decode(file_get_contents('php://input'), true);
if (empty($input['departmentName']) 
    || !isset($input['oldName']) 
    || !isset($input['newName'])
) {
    http_response_code(400);
    echo json_encode([
        'status'=>'error',
        'message'=>'departmentName, oldName and newName are required'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}


// تعقيم المدخلات
$dept   = preg_replace('/[^A-Za-z0-9_\-ا-ي ]/u','',$input['departmentName']);
$path   = str_replace(['..\\','../'], '', $input['path']);
$path   = trim(preg_replace('/[^A-Za-z0-9_\-ا-ي\/ ]/u','',$path),"/\\");
$old    = preg_replace('/[^A-Za-z0-9_\-ا-ي \.\-]/u','',$input['oldName']);
$new    = preg_replace('/[^A-Za-z0-9_\-ا-ي \.\-]/u','',$input['newName']);

// مسارات الملفات
$root      = realpath(dirname(__DIR__,2).'/library');
$baseDept  = $root.DIRECTORY_SEPARATOR.$dept;
$targetDir = $path===''? $baseDept : $baseDept.DIRECTORY_SEPARATOR.$path;
$oldPath   = $targetDir.DIRECTORY_SEPARATOR.$old;
$newPath   = $targetDir.DIRECTORY_SEPARATOR.$new;

if (!file_exists($oldPath)) {
    http_response_code(404);
    echo json_encode(['status'=>'error','message'=>'Item not found'],JSON_UNESCAPED_UNICODE);
    exit;
}
if (file_exists($newPath)) {
    http_response_code(409);
    echo json_encode(['status'=>'error','message'=>'Target name already exists'],JSON_UNESCAPED_UNICODE);
    exit;
}

if (!rename($oldPath, $newPath)) {
    $err = error_get_last();
    http_response_code(500);
    echo json_encode([
        'status'=>'error',
        'message'=>'Rename failed: '.$err['message']
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// في حال النجاح:
echo json_encode([
    'status' => 'success',
    'message'=> 'Renamed successfully'
], JSON_UNESCAPED_UNICODE);
exit;