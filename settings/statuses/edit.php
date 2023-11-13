<?php
session_start();
require_once '../../config.php';
require_once '../../includes/auth_validate.php';

$db = getDbInstance();

$post_data = filter_input_array(INPUT_POST);

$db->where('id',$post_data['id']);
$db->get('statuses');

if($db->count >=1 && !empty($post_data['name']) && !empty($post_data['color']) && $_SERVER['REQUEST_METHOD'] == 'POST'){
    $db->where('id',$post_data['id']);
    if ($db->update ('statuses', $post_data))
        echo json_encode($post_data);
    else
        echo json_encode(['error' => 'Ошибка изменения']);
} else {
    echo json_encode(['error' => 'Не заполнены нужные поля']);
}

?>