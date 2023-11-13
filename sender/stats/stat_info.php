<?php
session_start();
require_once '../../config.php';
require_once BASE_PATH_ADMIN . '/includes/auth_validate.php';

// Get Input data from query string
$search_string = filter_input(INPUT_GET, 'search_string');
$try_id = filter_input(INPUT_GET, 'try_id');

//Get DB instance. i.e instance of MYSQLiDB Library
$db = getDbInstance();

if (!empty($search_string)) {
    $db->where('name', '%' . $search_string . '%', 'like');
}

if(!empty($try_id)){
    $db->where("sendTryId", $try_id);
}

// Get result of the query.
$rows = $db->get('sender_log');
// print_r($rows);

include BASE_PATH_ADMIN . '/includes/header.php';
?>
<!-- Main container -->
<div id="page-wrapper" class="col-md-9 ms-sm-auto col-lg-10 px-md-4">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
            <h1 class="h2">Рассылки</h1>
        </div>
    </div>

    <?php include BASE_PATH_ADMIN . '/includes/flash_messages.php'; ?>

    <!-- Filters -->
    <div class="well text-center filter-form">
        <form class="form form-inline" action="">
            <div class="row g-3">
                <div class="col-md-9">
                    <input type="text" class="form-control" id="input_search" name="search_string" placeholder="Поиск" value="<?php echo xss_clean($search_string); ?>">
                </div>
                <div class="col-md-3">
                    <input type="submit" value="Применить" class="btn btn-primary">
                </div>
            </div>
        </form>
    </div>
    <hr>
    <!-- //Filters -->

    <!-- Table -->
    <table class="table table-striped ">
        <thead>
            <tr>
                <th width="15%">Дата</th>
                <th width="70%">Название</th>
                <th width="10%">Email</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $row) : ?>
                <tr>
                    <td><?php $date = new DateTime($row['created_at']); echo xss_clean($date->format('d-m-Y h:m')); ?></td>
                    <td><?php echo xss_clean($row['name']); ?></td>
                    <td><?php echo xss_clean($row['email']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <!-- //Table -->

</div>
<!-- //Main container -->
<?php include BASE_PATH_ADMIN . '/includes/footer.php'; ?>