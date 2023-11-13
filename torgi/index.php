<?php
require '../vendor/autoload.php';
session_start();
require_once '../config.php';
require_once BASE_PATH_ADMIN . '/includes/auth_validate.php';
require_once BASE_PATH_ADMIN . '/helpers/helpers.php';

//Get DB instance. i.e instance of MYSQLiDB Library
$db = getDbInstance();

// Per page limit for pagination.
$pagelimit = 20;

// Get current page.
$page = filter_input(INPUT_GET, 'page');
if (!$page) {
    $page = 1;
}

$select = array('t.id', 't.number', 't.title', 't.text', 't.sum', 't.step', 't.deposit', 't.debtor_id', 't.lot_created', 't.created_at', 't.dates_from', 't.dates_torg', 't.watched', 'd.name', 'd.inn', 'd.bo_nalog');
$db->join("debtors d", "t.debtor_id=d.debtor_id", "LEFT");

// Set pagination limit
$db->pageLimit = $pagelimit;
// Get result of the query.
$db->orderBy("id", "Desc");
$db->where ("inwork != 'true'");
$db->orWhere ("inwork", NULL, 'IS');
$rows = $db->arraybuilder()->paginate('torgi t', $page, $select);
$total_pages = $db->totalPages;


include BASE_PATH_ADMIN . '/includes/header.php';
?>
<!-- Main container -->
<main id="content" class="main">
    <div class="content container-fluid">

        <h1 class="page-header-title mb-3">Лоты</h1>

        <?php include BASE_PATH_ADMIN . '/includes/flash_messages.php'; ?>

        <!-- Table -->
        <div class="table-responsive datatable-custom">
            <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table table-striped">
                <thead>
                    <tr>
                        <th width="70%">Описание</th>
                        <th width="10%">Цены</th>
                        <!-- <th width="15%">Активы</th> -->
                        <th width="20%">Продавец</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row) : ?>
                        <tr id="torg-<?php echo $row['id']; ?>" class="animate__animated">
                            <td class="text-truncate" style="white-space: normal; max-width: 20%;">
                                <a href="https://tbankrot.ru/item?id=<?php echo $row['number'] ?>" class="icon-link" target="_blank" rel="noopener noreferrer">
                                    <?php echo $row['title'] ?> <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                                <span class="divider-end"><?php echo $row['lot_created'] ?></span>
                                
                                <p><?php print_r(search_inn($row['text'])) ?></p>
                                
                                <?php if(!empty($row['dates_from']) || !empty($row['dates_torg'])) { ?>
                                    <figure>
                                        <blockquote class="blockquote blockquote-sm">
                                            <p>
                                            <?php echo $row['dates_from']; ?>
                                            <br>
                                            <?php echo $row['dates_torg']; ?>
                                            </p>
                                        </blockquote>
                                    </figure>
                                <?php } ?>
                            </td>
                            <td>
                                <div class="watched-status mb-3">
                                    <div class="form-check form-check-switch">
                                        <input class="form-check-input" type="checkbox" value="" id="Checkbox<?php echo $row['id'] ?>" <?php echo $row['watched'] == 'true' ? 'checked' : '' ?> data-torgid="<?php echo $row['id'] ?>" >
                                        <label class="form-check-label btn-icon btn-xs rounded-circle" for="Checkbox<?php echo $row['id'] ?>" onclick="setWatchedStatus(this)">
                                            <span class="form-check-active" data-bs-toggle="tooltip" data-bs-placement="top" title="Просмотрен">
                                                <i class="bi bi-eye-fill"></i>
                                            </span>
                                            <span class="form-check-default" data-bs-toggle="tooltip" data-bs-placement="top" title="Не просмотрен">
                                                <i class="bi bi-eye-slash-fill"></i>
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                <span class="badge bg-dark" data-bs-toggle="tooltip" data-bs-html="true" title="Стартовая"><?php echo $row['sum']; ?> руб</span><br>
                                <span class="badge bg-success" data-bs-toggle="tooltip" data-bs-html="true" title="Текущая"><?php echo $row['step']; ?></span><br>
                                <span class="badge bg-danger" data-bs-toggle="tooltip" data-bs-html="true" title="Минимальная"><?php echo $row['deposit']; ?></span><br><br>

                                <button type="button" onclick="inworkEvent(this)" class="btn btn-primary inwork-btn" data-torgID="<?php echo $row['id']; ?>" data-bs-toggle="tooltip" data-bs-html="true" title="В работу">
                                    <i class="bi bi-folder-plus"></i>
                                </button>
                            </td>
                            <td style="white-space: initial;">

                                <?php if(!empty($row['inn'])) { ?>
                                    <mark id="<?php echo $row['inn']; ?>" style="cursor: pointer;" data-bs-toggle="modal" data-bs-inn="<?php echo $row['inn']; ?>" data-bs-target="#editUserModal"><?php echo $row['name']; ?></mark>
                                <?php } ?>
                                
                                <?php if(!empty($row['bo_nalog'])) { $bo_nalog = json_decode($row['bo_nalog'], true); ?>
                                    <br><br>
                                    <a href="https://bo.nalog.ru/organizations-card/<?php echo $bo_nalog['id'] ?>" class="icon-link" target="_blank" rel="noopener noreferrer">
                                        Отчетность <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                    <br>
                                    <?php array_multisort($bo_nalog['bfo'], SORT_DESC); foreach($bo_nalog['bfo'] as $bfo) { ?>
                                        <?php if($bfo['actives'] > 0) { ?>
                                            <?php  echo $bfo['period'] ?>г. - <span class="badge bg-soft-secondary text-secondary"><?php echo number_format($bfo['actives'] * 1000, 0, ',', ' ') ?> ₽</span><br>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!-- //Table -->

        <!-- Pagination -->
        <div class="text-center">
            <?php echo paginationLinks($page, $total_pages, 'torgi/index.php'); ?>
        </div>
        <!-- //Pagination -->

    </div><!-- container-fluid -->

</main><!-- /#page-wrapper -->
<!-- //Main container -->

<script>
    async function inworkEvent (el) {
        const torgID = el.getAttribute('data-torgid') ? el.getAttribute('data-torgid') : false;
        if(torgID) {
            const formData = new FormData();
            formData.append("id", torgID);
            formData.append("inwork", 'true');
            let inwork = await sendData(formData, 'setInwork');
            console.log(inwork);
            if(inwork?.id) {
                const torgRow = document.querySelector(`tr#torg-${inwork.id}`);
                torgRow.classList.add('animate__fadeOutLeft');
                torgRow.addEventListener('animationend', () => {
                    torgRow.remove();
                });
            }


        }
    }

    function setWatchedStatus(el) {
        if(el?.control) {
            const torgID = el?.control.getAttribute('data-torgid') ? el?.control.getAttribute('data-torgid') : false;
            const watched = el?.control?.checked ? 'false' : 'true';
            const formData = new FormData();
            formData.append("id", torgID);
            formData.append("watched", watched);
            sendData(formData, 'setWatched');
        }
    }
    
    async function sendData(data, meth) {
        try {
            const response = await fetch(`torgi/${meth}.php`, {
                method: "POST",
                body: data,
                headers: {
                    'X-Requested-With' : 'XMLHttpRequest'
                }
            });
            const result = await response.json();
            if(!result.error) {
                return result;
            } else {
                alert(result?.error);
            }
        } catch (error) {
            console.error('Ошибка:', error);
        }
    }
</script>

<?php include BASE_PATH_ADMIN . '/includes/footer.php'; ?>