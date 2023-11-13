<?php
session_start();
require_once '../config.php';
require_once BASE_PATH_ADMIN . '/includes/auth_validate.php';

// category class
require_once BASE_PATH . '/lib/products/Category.php';
$category = new Category();

// Get Input data from query string
$search_string = filter_input(INPUT_GET, 'search_string');
$filter_col = filter_input(INPUT_GET, 'filter_col');
$order_by = filter_input(INPUT_GET, 'order_by');

// Per page limit for pagination.
$pagelimit = 15;

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
$select = array('id', 'name', 'image', 'description');

//Start building query according to input parameters.
// If search string
if ($search_string) {
    $db->where('name', '%' . $search_string . '%', 'like');
    $db->orwhere('description', '%' . $search_string . '%', 'like');
}

//If order by option selected
if ($order_by) {
    $db->orderBy($filter_col, $order_by);
}

// Set pagination limit
$db->pageLimit = $pagelimit;

// Get result of the query.
$rows = $db->arraybuilder()->paginate('prod_category', $page, $select);
$total_pages = $db->totalPages;

include BASE_PATH_ADMIN . '/includes/header.php';
?>
<!-- Main container -->
<div id="page-wrapper" class="col-md-9 ms-sm-auto col-lg-10 px-md-4">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Категории товаров</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="add_category.php?operation=create" class="btn btn-success"><i class="bi bi-plus"></i> Добавить</a>
            </div>
        </div>
    </div>

    <?php include BASE_PATH_ADMIN . '/includes/flash_messages.php'; ?>

    <!-- Filters -->
    <div class="well text-center filter-form">
        <form class="form form-inline" action="">
            <div class="row g-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" id="input_search" name="search_string" placeholder="Поиск" value="<?php echo xss_clean($search_string); ?>">
                </div>

                <div class="col-md-7">
                    <div class="input-group">
                        <span class="input-group-text">Сортировка</span>
                        <select name="filter_col" class="form-select">
                            <?php
                            foreach ($category->setOrderingValues() as $opt_value => $opt_name) : ($order_by === $opt_value) ? $selected = 'selected' : $selected = '';
                                echo ' <option value="' . $opt_value . '" ' . $selected . '>' . $opt_name . '</option>';
                            endforeach;
                            ?>
                        </select>

                        <select name="order_by" class="form-select" id="input_order">
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
                <div class="col-md-2">
                    <a href="category.php" class="btn btn-primary"><i class="bi bi-x-circle"></i> Сбросить</a>
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
                <th width="45%">Название</th>
                <th width="20%">Фото</th>
                <th width="20%">Описание</th>
                <th width="10%"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $row) : ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo xss_clean($row['name']); ?></td>
                    <td><?php if($row['image']) { echo '<img src="' . $row['image'] . '" class="img-thumbnail" width="80px"/>'; } ?></td>
                    <td><?php echo xss_clean($row['description']); ?></td>
                    <td>
                        <div class="d-grid gap-2 d-md-block">
                            <a href="edit_category.php?category_id=<?php echo $row['id']; ?>&operation=edit" class="btn btn-primary" title="Изменить"><i class="bi bi-pencil-square"></i></a>
                            <button type="button" title="Удалить" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirm-delete-<?php echo $row['id']; ?>"><i class="bi bi-trash"></i></button>
                        </div>
                    </td>
                </tr>

                <!-- Modal -->
                <div class="modal fade" id="confirm-delete-<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="confirm-delete-<?php echo $row['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <form action="delete_category.php" method="POST">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Подтверждение удаления</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="del_id" id="del_id" value="<?php echo $row['id']; ?>">
                                    <p>Уверены что хотите удалить?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-danger pull-left">Да</button>
                                    <button type="button" class="btn btn-secondary " data-bs-dismiss="modal">Нет</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>


            <?php endforeach; ?>
        </tbody>
    </table>
    <!-- //Table -->

    <!-- Pagination -->
    <div class="text-center">
        <?php echo paginationLinks($page, $total_pages, 'categorys.php'); ?>
    </div>
    <!-- //Pagination -->
</div>
<!-- //Main container -->
<?php include BASE_PATH_ADMIN . '/includes/footer.php'; ?>

<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'))
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>