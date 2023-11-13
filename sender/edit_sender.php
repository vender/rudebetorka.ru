<?php
session_start();
require_once '../config.php';
require_once BASE_PATH_ADMIN . '/includes/auth_validate.php';

$sender_id = filter_input(INPUT_GET, 'sender_id', FILTER_VALIDATE_INT);
$operation = filter_input(INPUT_GET, 'operation',FILTER_SANITIZE_STRING); 
($operation == 'edit') ? $edit = true : $edit = false;
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

//serve POST method, After successful insert, redirect to sender page.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //Mass Insert Data. Keep "name" attribute in html form same as column name in mysql table.
    $data_to_update = $_POST;
    //Insert timestamp
    $data_to_update['updated_at'] = date('Y-m-d H:i:s');
    $data_to_update['user-list'] = json_encode($data_to_update['user-list']);
    $db->where('id',$sender_id);
    $stat = $db->update('sender', $data_to_update);

    if($stat) {
        $_SESSION['success'] = "Рассылка изменена!";
        //Redirect to the listing page,
        header('location: index.php');
        //Important! Don't execute the rest put the exit/die. 
        exit();
    }
}

//If edit variable is set, we are performing the update operation.
if($edit) {
    $db->where('id', $sender_id);
    $sender = $db->getOne("sender");
    // print_r($sender['user-list']);
}

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