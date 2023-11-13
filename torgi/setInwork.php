<?php
require '../vendor/autoload.php';
session_start();
require_once '../config.php';
require_once BASE_PATH_ADMIN . '/includes/auth_validate.php';

$db = getDbInstance();

$post_data = filter_input_array(INPUT_POST);

$db->where('id',$post_data['id']);
$db->get('torgi');

if($db->count >=1 && !empty($post_data['inwork']) && $_SERVER['REQUEST_METHOD'] == 'POST'){
    $db->where('id',$post_data['id']);
    if ($db->update('torgi', ['inwork' => $post_data['inwork'], 'status_id' => 1]))
        echo json_encode($post_data);
    else
        echo json_encode(['error' => 'Ошибка изменения']);
} else {
    echo json_encode(['error' => 'Не заполнены нужные поля']);
}

?>