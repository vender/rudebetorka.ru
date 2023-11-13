<?php 
session_start();
require_once 'includes/auth_validate.php';
require_once './config.php';
$del_id = filter_input(INPUT_POST, 'del_id');
if ($del_id && $_SERVER['REQUEST_METHOD'] == 'POST') 
{

	if($_SESSION['admin_type']!='super'){
		$_SESSION['failure'] = "У вас нет прав для этого";
    	header('location: category.php');
        exit;

	}
    $category_id = $del_id;

    $db = getDbInstance();
    $db->where('id', $category_id);
    $status = $db->delete('prod_category');
    
    if ($status) 
    {
        $_SESSION['info'] = "Категория успешно удалена!";
        header('location: category.php');
        exit;
    }
    else
    {
    	$_SESSION['failure'] = "Ошибка удаления категории.";
    	header('location: category.php');
        exit;

    }
    
}