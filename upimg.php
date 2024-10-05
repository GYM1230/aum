<?php

header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
date_default_timezone_set('Asia/Shanghai');

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action == 'delete') {
    $image = isset($_GET['image']) ? $_GET['image'] : '';
    $image_name = basename($image);
    $uploadDir = 'images' . DIRECTORY_SEPARATOR . date("Y-m-d") . DIRECTORY_SEPARATOR;
    $dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . $uploadDir;
    $image_path = $dir . $image_name;
    if (file_exists($image_path)) {
        unlink($image_path);
        $json = '{"code":0,"msg":"图片删除成功！"}';
        echo $json;
    } else {
        $json = '{"code":1,"msg":"图片不存在！"}';
        echo $json;
    }
} else {
    if (isset($_FILES['file'])) {
        if (is_uploaded_file($_FILES['file']['tmp_name'])) {
            $ext_suffix = pathinfo($_FILES["file"]["name"])['extension'];
            $allow_suffix = array('jpg','gif','jpeg','png','mp3');
            if (!in_array($ext_suffix, $allow_suffix)) {
                $json = '{"code":1,"msg":"抱歉，您上传的数据格式不支持！"}';
                echo $json;
            } else {
                $ret = array();
                $uploadDir = 'images' . DIRECTORY_SEPARATOR . date("Y-m-d") . DIRECTORY_SEPARATOR;
                $dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . $uploadDir;
                file_exists($dir) || (mkdir($dir, 0777, true) && chmod($dir, 0777));
                if (!is_array($_FILES["file"]["name"])) { // 单个文件
                    $fileName = time() . uniqid() . '.' . pathinfo($_FILES["file"]["name"])['extension'];
                    move_uploaded_file($_FILES["file"]["tmp_name"], $dir . $fileName);
                    $currentPath = dirname($_SERVER['PHP_SELF']);
                    $ret['file'] = get_http_type() . $_SERVER['SERVER_NAME'] . $currentPath . DIRECTORY_SEPARATOR . $uploadDir . $fileName;
                }
                $json = '{"code":0,"msg":"' . $ret['file'] . '"}';
                echo $json;
            }
        } else {
            $json = '{"code":1,"msg":"抱歉，您上传的数据不是图片！"}';
            echo $json;
        }
    }
}

function get_http_type()
{
    $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
    return $http_type;
}
?>
