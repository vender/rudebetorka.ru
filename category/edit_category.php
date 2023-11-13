<?php
session_start();
require_once './config.php';
require_once 'includes/auth_validate.php';


// Sanitize if you want
$category_id = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);
$operation = filter_input(INPUT_GET, 'operation'); 
($operation == 'edit') ? $edit = true : $edit = false;
 $db = getDbInstance();

//Handle update request. As the form's action attribute is set to the same script, but 'POST' method, 
if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
    //Get category id form query string parameter.
    $category_id = filter_input(INPUT_GET, 'category_id');

    //Get input data
    $data_to_update = filter_input_array(INPUT_POST);
    
    $data_to_update['updated_at'] = date('Y-m-d H:i:s');
    $db = getDbInstance();
    $db->where('id',$category_id);
    $stat = $db->update('prod_category', $data_to_update);

    if($stat)
    {
        $_SESSION['success'] = "категория изменена.";
        //Redirect to the listing page,
        header('location: category.php');
        //Important! Don't execute the rest put the exit/die. 
        exit();
    }
}


//If edit variable is set, we are performing the update operation.
if($edit)
{
    $db->where('id', $category_id);
    //Get data to pre-populate the form.
    $category = $db->getOne("prod_category");
}
?>


<?php
    include_once 'includes/header.php';
?>
<div id="page-wrapper" class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="row">
        <h2 class="page-header">Изменение каткгории</h2>
    </div>
    <!-- Flash messages -->
    <?php
        include('./includes/flash_messages.php')
    ?>

    <form class="" action="" method="post" enctype="multipart/form-data" id="contact_form">
        
        <?php
            //Include the common form for add and edit  
            require_once('./forms/category_form.php'); 
        ?>
    </form>
</div>


<script>
    // function responsive_filemanager_callback(field_id){
    //     console.log(field_id);
	// 	if(field_id){
			
	// 		var url=jQuery('#'+field_id).val();
	// 	}
	// }
    function open_popup(url) {
        var w = 880;
        var h = 570;
        var l = Math.floor((screen.width-w)/2);
        var t = Math.floor((screen.height-h)/2);
        var win = window.open(url, 'ResponsiveFilemanager', "scrollbars=1,width=" + w + ",height=" + h + ",top=" + t + ",left=" + l);
    }
</script>

<?php include_once 'includes/footer.php'; ?>