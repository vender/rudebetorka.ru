<?php
session_start();
require_once '../../config.php';
require_once '../../includes/auth_validate.php';

$del_id = filter_input(INPUT_POST, 'del_id');

$db = getDbInstance();

if($_SESSION['admin_type']!='super'){
    header('HTTP/1.1 401 Unauthorized', true, 401);
    exit("401 Unauthorized");
}

// Delete a user using user_id
if ($del_id && $_SERVER['REQUEST_METHOD'] == 'POST' && $del_id != 1) {
    $db->where('id', $del_id);
    $stat = $db->delete('statuses');
    if ($stat) {
        header('location: index.php');
        exit;
    } else {
        echo json_encode(['error' => 'Ошибка удаления']);
    }
} else {
    echo json_encode(['error' => 'Ошибка удаления']);
}

?>