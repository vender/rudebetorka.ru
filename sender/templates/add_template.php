<?php
session_start();
require_once '../../config.php';
require_once BASE_PATH_ADMIN . '/includes/auth_validate.php';

date_default_timezone_set('Etc/UTC');
$subFolder = time();

//serve POST method, After successful insert
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data_to_store = array_filter($_POST);

    if($_FILES['atachments']['error'][0] == 0 && $_FILES['atachments']['size'] > 0){
        if (!file_exists("../atachments/".$subFolder)) {
            mkdir("../atachments/".$subFolder, 0755, true);
        }
        
        foreach ($_FILES["atachments"]['tmp_name'] as $key => $ct) {
            $file_name = str_replace(array(" ", '"','_',',', "'", "&", "/", "\\", "?", "#"), '_', $_FILES["atachments"]['name'][$key]);

            if (move_uploaded_file($ct, "../atachments/".$subFolder."/". $file_name)) {
                $atachments[] = "atachments/".$subFolder."/". $file_name;
            } else {
                $response = [
                    "status" => false,
                    "message" => 'Failed to move file'
                ];
            }
        }

        $data_to_store['atachments'] = json_encode($atachments, JSON_UNESCAPED_UNICODE);
    }

    //Insert timestamp
    $data_to_store['created_at'] = date('Y-m-d H:i:s');
    
    // print_r($data_to_store);

    $db = getDbInstance();
    
    $last_id = $db->insert('sender_templates', $data_to_store);

    if($last_id) {
    	$_SESSION['success'] = "Шаблон добавлен!";
    	header('location: index.php');
    	exit();
    }
    else {
        echo 'insert failed: ' . $db->getLastError();
        exit();
    }
}

//We are using same form for adding and editing. This is a create form so declare $edit = false.
$edit = false;

include BASE_PATH_ADMIN . '/includes/header.php';
?>
<div id="page-wrapper" class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Добавить шаблон</h1>
    </div>
    <form class="form" action="" method="post"  id="template_form" enctype="multipart/form-data">
       <?php  include_once('template_form.php'); ?>
    </form>
</div>


<?php include BASE_PATH_ADMIN . '/includes/footer.php'; ?>