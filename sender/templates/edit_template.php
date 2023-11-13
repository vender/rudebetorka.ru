<?php
session_start();
require_once '../../config.php';
require_once BASE_PATH_ADMIN . '/includes/auth_validate.php';


// Sanitize if you want
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$operation = filter_input(INPUT_GET, 'operation',FILTER_SANITIZE_STRING); 
($operation == 'edit') ? $edit = true : $edit = false;
 $db = getDbInstance();
 $subFolder = time();

//Handle update request. As the form's action attribute is set to the same script, but 'POST' method, 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //Get event id form query string parameter.
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);

    //Get input data
    $input_data = filter_input_array(INPUT_POST);
    $data_to_update = $input_data;
    
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

        $data_to_update['atachments'] = json_encode($atachments, JSON_UNESCAPED_UNICODE);
    }

    if(!empty($input_data['atachments'])) {
        $data_to_update['atachments'] = json_encode(explode(',', $data_to_update['atachments']), JSON_UNESCAPED_UNICODE);
    }

    $data_to_update['updated_at'] = date('Y-m-d H:i:s');
    $db = getDbInstance();
    $db->where('id',$id);
    $stat = $db->update('sender_templates', $data_to_update);

    if($stat){
        $_SESSION['success'] = "Шаблон изменен!";
        //Redirect to the listing page,
        header('location: index.php');
        //Important! Don't execute the rest put the exit/die. 
        exit();
    }
}


//If edit variable is set, we are performing the update operation.
if($edit)
{
    $db->where('id', $id);
    //Get data to pre-populate the form.
    $sender_templates = $db->getOne("sender_templates");
}
?>


<?php
    include BASE_PATH_ADMIN . '/includes/header.php';
?>
<div id="page-wrapper" class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Изменить участника</h1>
    </div>
    <!-- Flash messages -->
    <?php include BASE_PATH_ADMIN . '/includes/flash_messages.php'; ?>

    <form class="" action="" method="post" enctype="multipart/form-data" id="template_form">
    <?php  include_once('template_form.php'); ?>
    </form>
</div>




<?php include BASE_PATH_ADMIN . '/includes/footer.php'; ?>