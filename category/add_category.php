<?php
session_start();
require_once './config.php';
require_once './includes/auth_validate.php';


//serve POST method, After successful insert, redirect to category.php page.
if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    //Mass Insert Data. Keep "name" attribute in html form same as column name in mysql table.
    $data_to_store = array_filter($_POST);
    
    //Insert timestamp
    $data_to_store['created_at'] = date('Y-m-d H:i:s');
    $db = getDbInstance();
    
    $last_id = $db->insert('prod_category', $data_to_store);
    
    if($last_id)
    {
    	$_SESSION['success'] = "категория добавлена!";
    	header('location: category.php');
    	exit();
    }
    else
    {
        echo 'insert failed: ' . $db->getLastError();
        exit();
    }
}

//We are using same form for adding and editing. This is a create form so declare $edit = false.
$edit = false;

require_once 'includes/header.php'; 
?>
<div id="page-wrapper" class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Добавление категории</h1>
    </div>
    <form class="form" action="" method="post"  id="category_form" enctype="multipart/form-data">
       <?php  include_once('./forms/category_form.php'); ?>
    </form>
</div>

<script>
    function open_popup(url) {
        var w = 880;
        var h = 570;
        var l = Math.floor((screen.width-w)/2);
        var t = Math.floor((screen.height-h)/2);
        var win = window.open(url, 'ResponsiveFilemanager', "scrollbars=1,width=" + w + ",height=" + h + ",top=" + t + ",left=" + l);
    }
</script>

<?php include_once 'includes/footer.php'; ?>