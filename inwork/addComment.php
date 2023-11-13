<?php
require '../vendor/autoload.php';
session_start();
require_once '../config.php';
require_once BASE_PATH_ADMIN . '/includes/auth_validate.php';

$db = getDbInstance();

$post_data = filter_input_array(INPUT_POST);

// $db->where('id',$post_data['id']);
// $db->get('comments');

if(!empty($post_data['torg_id']) && !empty($post_data['comment']) && $_SERVER['REQUEST_METHOD'] == 'POST'){
    // $db->where('id',$post_data['id']);
    if ($db->insert('comments', $post_data))
        echo json_encode($post_data);
    else
        echo json_encode(['error' => 'Ошибка изменения']);
} else {
    echo json_encode(['error' => 'Не заполнены нужные поля']);
}

?>