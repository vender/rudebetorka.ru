<?php
session_start();
require_once 'config.php';
require_once BASE_PATH_ADMIN . '/includes/auth_validate.php';

// Users class
require_once BASE_PATH . '/lib/Users/Users.php';
$users = new Users();

// Only super admin is allowed to access this page
if ($_SESSION['admin_type'] !== 'super') {
    // Show permission denied message
    header('HTTP/1.1 401 Unauthorized', true, 401);
    exit('401 Unauthorized');
}

// Get Input data from query string
$search_string = filter_input(INPUT_GET, 'search_string');
$filter_col = filter_input(INPUT_GET, 'filter_col');
$order_by = filter_input(INPUT_GET, 'order_by');
$del_id = filter_input(INPUT_GET, 'del_id');

// Per page limit for pagination.
$pagelimit = 20;

// Get current page.
$page = filter_input(INPUT_GET, 'page');
if (!$page) {
    $page = 1;
}

// If filter types are not selected we show latest added data first
if (!$filter_col) {
    $filter_col = 'id';
}
if (!$order_by) {
    $order_by = 'Desc';
}

//Get DB instance. i.e instance of MYSQLiDB Library
$db = getDbInstance();
$select = array('id', 'user_name', 'admin_type');

//Start building query according to input parameters.
// If search string
if ($search_string) {
    $db->where('user_name', '%' . $search_string . '%', 'like');
}

//If order by option selected
if ($order_by) {
    $db->orderBy($filter_col, $order_by);
}

// Set pagination limit
$db->pageLimit = $pagelimit;

// Get result of the query.
$rows = $db->arraybuilder()->paginate('admin_accounts', $page, $select);
$total_pages = $db->totalPages;

include BASE_PATH_ADMIN . '/includes/header.php';
?>
<!-- Main container -->
<main id="content" class="main">
    <div class="content container-fluid">

        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                <h1 class="h2">Admin users</h1>
            </div>
            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group me-2">
                    <a href="add_admin.php" class="btn btn-success"><i class="bi bi-plus"></i> Добавить</a>
                </div>
            </div>
        </div>

        <?php include BASE_PATH_ADMIN . '/includes/flash_messages.php'; ?>

        <?php
        if (isset($del_stat) && $del_stat == 1) {
            echo '<div class="alert alert-info">Successfully deleted</div>';
        }
        ?>

        <div class="well text-center filter-form">
            <form class="form form-inline" action="">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" class="form-control" id="input_search" name="search_string" placeholder="Поиск" value="<?php echo $search_string; ?>">
                    </div>

                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-text">Сортировка</span>
                            <select name="filter_col" class="form-control">
                                <?php
                                foreach ($users->setOrderingValues() as $opt_value => $opt_name) : ($order_by === $opt_value) ? $selected = 'selected' : $selected = '';
                                    echo ' <option value="' . $opt_value . '" ' . $selected . '>' . $opt_name . '</option>';
                                endforeach;
                                ?>
                            </select>

                            <select name="order_by" class="form-control" id="input_order">
                                <option value="Asc" <?php
                                                    if ($order_by == 'Asc') {
                                                        echo 'selected';
                                                    }
                                                    ?>>Asc</option>
                                <option value="Desc" <?php
                                                        if ($order_by == 'Desc') {
                                                            echo 'selected';
                                                        }
                                                        ?>>Desc</option>
                            </select>
                            <input type="submit" value="Применить" class="btn btn-primary">
                        </div>
                    </div>

                </div>
            </form>
        </div>
        <hr>

        <!-- Table -->
        <div class="table-responsive datatable-custom">
        <table class="js-datatable table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
            <thead>
                <tr>
                    <th width="5%">ID</th>
                    <th width="45%">Name</th>
                    <th width="40%">Admin type</th>
                    <th width="10%">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row) : ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['user_name']; ?></td>
                        <td><?php echo $row['admin_type']; ?></td>
                        <td>
                            <a href="edit_admin.php?admin_user_id=<?php echo $row['id']; ?>&operation=edit" class="btn btn-primary btn-sm"><i class="bi bi-pencil-square"></i></a>
                            <a href="#" class="btn btn-danger btn-sm delete_btn" data-toggle="modal" data-target="#confirm-delete-<?php echo $row['id']; ?>"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                    <!-- Delete Confirmation Modal -->
                    <div class="modal fade" id="confirm-delete-<?php echo $row['id']; ?>" role="dialog">
                        <div class="modal-dialog">
                            <form action="delete_user.php" method="POST">
                                <!-- Modal content -->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Confirm</h4>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="del_id" id="del_id" value="<?php echo $row['id']; ?>">
                                        <p>Are you sure you want to delete this row?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-default pull-left">Yes</button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- //Delete Confirmation Modal -->
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <!-- //Table -->

        <!-- Pagination -->
        <div class="text-center">
            <?php
            if (!empty($_GET)) {
                // We must unset $_GET[page] if previously built by http_build_query function
                unset($_GET['page']);
                // To keep the query sting parameters intact while navigating to next/prev page,
                $http_query = "?" . http_build_query($_GET);
            } else {
                $http_query = "?";
            }
            // Show pagination links
            if ($total_pages > 1) {
                echo '<ul class="pagination text-center">';
                for ($i = 1; $i <= $total_pages; $i++) {
                    ($page == $i) ? $li_class = ' class="active"' : $li_class = '';
                    echo '<li' . $li_class . '><a href="admin_users.php' . $http_query . '&page=' . $i . '">' . $i . '</a></li>';
                }
                echo '</ul>';
            }
            ?>
        </div>
        <!-- //Pagination -->
    </div>
</main>
<!-- //Main container -->
<?php include BASE_PATH_ADMIN . '/includes/footer.php'; ?>