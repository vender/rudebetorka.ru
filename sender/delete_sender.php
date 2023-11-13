<?php
session_start();
require_once '../config.php';
require_once BASE_PATH_ADMIN . '/includes/auth_validate.php';

$del_id = filter_input(INPUT_POST, 'del_id');
if ($del_id && $_SERVER['REQUEST_METHOD'] == 'POST') {

	if($_SESSION['admin_type']!='super'){
		$_SESSION['failure'] = "У вас нет прав на это действие";
    	header('location: sender');
        exit;

	}

    $db = getDbInstance();
    $db->where('id', $del_id);
    $status = $db->delete('sender');
    
    if ($status) 
    {
        $_SESSION['info'] = "Рассылка удалена!";
        header('location: '.BASE_URI.'sender');
        exit;
    }
    else
    {
    	$_SESSION['failure'] = "Ошибка удаления";
    	header('location: '.BASE_URI.'sender');
        exit;

    }
    
}