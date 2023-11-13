<?php
session_start();
require_once '../config.php';
require_once BASE_PATH_ADMIN . '/includes/auth_validate.php';

$db = getDbInstance();

$select = array('c.id', 'c.name', 'c.socilas', 'c.phone', 'c.url', 'c.fio', 'c.email', 'c.region', 'c.created_at', 'c.updated_at', 'c.status', 'r.name as regionName', 'r.id as regionId', 's.name as statusname', 's.color');
$db->join("regions r", "c.region=r.id", "LEFT");
$db->join("statuses s", "c.status=s.id", "LEFT");

$db->orderBy('regionName', 'ASC');
$customers = $db->get('customers c', null, $select);

$select_detstvo = array('cd.id', 'cd.name', 'cd.region', 'cd.status', 'r.name as regionName', 'r.id as regionId');
$db->join("regions r", "cd.region=r.id", "LEFT");

$db->orderBy('regionName', 'ASC');
$customers_detstvo = $db->get('customers_detstvo cd', null, $select_detstvo);

function map_region($customer) {
    return [$customer['regionId'] => $customer['regionName']];
}

$allCustomers = array_merge($customers, $customers_detstvo);

$filteredRegions = array_unique(array_map(function($customer) {
    return [$customer['regionName'], $customer['regionId']];
}, $allCustomers), SORT_REGULAR);

$templates = $db->get('sender_templates', null, ['id', 'name']);


//serve POST method, After successful insert, redirect to events.php page.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //Mass Insert Data. Keep "name" attribute in html form same as column name in mysql table.
    $data_to_store = array_filter($_POST);
    //Insert timestamp
    $data_to_store['created_at'] = date('Y-m-d H:i:s');
    
    $data_to_store['user-list'] = json_encode($data_to_store['user-list']);
    // print_r($data_to_store);
    
    $last_id = $db->insert('sender', $data_to_store);

    if($last_id) {
    	$_SESSION['success'] = "Рассылка добавлена!";
    	header('location: edit_sender.php?sender_id='.$last_id.'&operation=edit');
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
        <h1 class="h2">Добавить рассылку</h1>
    </div>
    <form class="form" action="" method="post"  id="sender_form" enctype="multipart/form-data">
       <?php  include_once('sender_form.php'); ?>
    </form>
</div>


<?php include BASE_PATH_ADMIN . '/includes/footer.php'; ?>