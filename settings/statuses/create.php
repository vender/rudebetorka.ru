<?php
session_start();
require_once '../../config.php';
require_once '../../includes/auth_validate.php';

// $status_id = filter_input(INPUT_POST, 'status_id');

$db = getDbInstance();

if(!empty($_POST['name']) && !empty($_POST['color']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $last_id = $db->insert ('statuses', $_POST);
} else {
    $last_id = ['error' => 'Ошибка добавления'];
}

print_r(json_encode($last_id));
?>