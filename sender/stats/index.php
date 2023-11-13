<?php
session_start();
require_once '../../config.php';
require_once BASE_PATH_ADMIN . '/includes/auth_validate.php';

// Costumers class
// require_once BASE_PATH . '/lib/Costumers/Costumers.php';
// $costumers = new Costumers();

// Get Input data from query string
$search_string = filter_input(INPUT_GET, 'search_string');

// Per page limit for pagination.
$pagelimit = 15;

// Get current page.
$page = filter_input(INPUT_GET, 'page');
if (!$page) {
    $page = 1;
}

//Get DB instance. i.e instance of MYSQLiDB Library
$db = getDbInstance();
$select = array('id', 'try_id', 'template_name', 'created_at', 'sender_name');

//Start building query according to input parameters.
// If search string
if ($search_string) {
    $db->where('sender_name', '%' . $search_string . '%', 'like');
}

// Set pagination limit
$db->pageLimit = $pagelimit;

// Get result of the query.
$rows = $db->arraybuilder()->paginate('sender_try_id', $page, $select);
$total_pages = $db->totalPages;

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


    <div id="export-section">

    </div>

    <!-- Table -->
    <table class="table table-striped ">
        <thead>
            <tr>
                <th width="5%">ID</th>
                <th width="15%">Дата</th>
                <th width="70%">Название</th>
                <th width="5%">Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $row) : ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['created_at']; ?></td>
                    <td><?php echo xss_clean($row['sender_name']); ?></td>
                    <td>
                        <a href="sender/stats/stat_info.php?try_id=<?php echo $row['try_id']; ?>&operation=edit" class="btn btn-primary btn-sm"><i class="bi bi-eye-fill"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <!-- //Table -->

    <!-- Pagination -->
    <div class="text-center">
        <?php echo paginationLinks($page, $total_pages, 'sender/index.php'); ?>
    </div>
    <!-- //Pagination -->
</div>
<!-- //Main container -->
<?php include BASE_PATH_ADMIN . '/includes/footer.php'; ?>

<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('title'))
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>