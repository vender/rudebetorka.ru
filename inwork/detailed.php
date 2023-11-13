<?php
session_start();
require_once '../config.php';
require_once BASE_PATH_ADMIN . '/includes/auth_validate.php';
require_once BASE_PATH_ADMIN . '/helpers/helpers.php';

//Get DB instance. i.e instance of MYSQLiDB Library
$db = getDbInstance();

$torg_id = filter_input(INPUT_GET, 'torg_id');

$select = array('t.id', 't.number', 't.title', 't.text', 't.sum', 't.step', 't.deposit', 't.debtor_id', 't.lot_created', 't.created_at', 't.dates_from', 't.dates_torg', 't.watched', 'd.name', 'd.inn', 'd.bo_nalog');
$db->join("debtors d", "t.debtor_id=d.debtor_id", "LEFT");

if (!empty($torg_id)) {
    $db->where('t.id', $torg_id);
    $torg_info = $db->getOne('torgi t', null, $select);
    
    $db->where('torg_id', $torg_id);
    $comments = $db->get('comments');
} else {
    exit('Не верный ID лота');
}

include BASE_PATH_ADMIN . '/includes/header.php';
?>

<main id="content" class="main">
    <div class="content container-fluid">

        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                <h1 class="h2">
                    <?php echo $torg_info['title'] ?>
                    <a href="https://tbankrot.ru/item?id=<?php echo $torg_info['number'] ?>" class="icon-link" target="_blank" rel="noopener noreferrer">
                        <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                </h1>
            </div>
            <span class="divider-end"><?php echo $torg_info['lot_created'] ?></span>
        </div>

        <div class="row">
            <div class="col-sm-6 col-xl-2 mb-3 mb-xl-6">
                <!-- Card -->
                <div class="card card-sm h-100">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="bi-receipt nav-icon"></i>
                            </div>

                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-1">Стартовая</h4>
                                <span class="d-block text-primary-dark"><?php echo $torg_info['sum']; ?> руб</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Card -->
            </div>

            <div class="col-sm-6 col-xl-2 mb-3 mb-xl-6">
                <!-- Card -->
                <div class="card card-sm h-100">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="bi-receipt nav-icon"></i>
                            </div>

                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-1">Текущая</h4>
                                <span class="d-block text-success"><?php echo $torg_info['step']; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Card -->
            </div>

            <div class="col-sm-6 col-xl-2 mb-3 mb-xl-6">
                <!-- Card -->
                <div class="card card-sm h-100">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="bi-receipt nav-icon"></i>
                            </div>

                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-1">Минимальная</h4>
                                <span class="d-block text-danger"><?php echo $torg_info['deposit']; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Card -->
            </div>

            <div class="col-sm-6 col-xl-6 mb-3 mb-xl-6">
                <!-- Card -->
                <div class="card card-sm h-100">
                    <div class="card-body">
                        <?php if (!empty($torg_info['dates_from']) || !empty($torg_info['dates_torg'])) { ?>
                            <blockquote class="blockquote blockquote-sm">
                                <?php echo $torg_info['dates_from']; ?>
                                <br>
                                <?php echo $torg_info['dates_torg']; ?>
                            </blockquote>
                        <?php } ?>
                    </div>
                </div>
                <!-- End Card -->
            </div>

        </div>

        <!-- Описание лота -->
        <div class="card mb-3 mb-lg-5">
            <div class="card-body">
                <p><?php print_r(search_inn($torg_info['text'])) ?></p>
            </div>
        </div>

        <hr class="my-0">

        <div class="row">

            <div class="col-lg-5 mb-3 mb-lg-5">

                <!-- Должник -->
                <div class="card card-centered bg-light h-100 rounded-0 shadow-none">
                    <div class="card-body">
                        <?php if (!empty($torg_info['inn'])) { ?>
                            <mark id="<?php echo $torg_info['inn']; ?>" style="cursor: pointer;" data-bs-toggle="modal" data-bs-inn="<?php echo $torg_info['inn']; ?>" data-bs-target="#editUserModal"><?php echo $torg_info['name']; ?></mark>
                        <?php } ?>

                        <?php if (!empty($torg_info['bo_nalog'])) {
                            $bo_nalog = json_decode($torg_info['bo_nalog'], true); ?>
                            <br>
                            <a href="https://bo.nalog.ru/organizations-card/<?php echo $bo_nalog['id'] ?>" class="icon-link" target="_blank" rel="noopener noreferrer">
                                Отчетность <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                            <br>
                            <div class="bfo-wrapp">
                                <?php array_multisort($bo_nalog['bfo'], SORT_DESC);
                                foreach ($bo_nalog['bfo'] as $bfo) { ?>
                                    <?php if ($bfo['actives'] > 0) { ?>
                                        <?php echo $bfo['period'] ?>г. - <span class="badge bg-soft-secondary text-secondary"><?php echo number_format($bfo['actives'] * 1000, 0, ',', ' ') ?> ₽</span><br>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <!-- Коментарии -->
            <div class="col-lg-7 mb-3 mb-lg-5">
                <div class="card-header card-header-content-between">
                    <h4 class="card-header-title">Коментарии</h4>

                    <button type="button" class="btn btn-soft-info btn-icon btn-sm" data-bs-toggle="modal" data-bs-target="#commentModal">
                        <i class="bi bi-plus"></i>
                    </button>
                </div>

                <div class="card-body card-body-height">
                    <ul id="commentList" class="list-group list-group-flush list-group-start-bordered">
                        <?php foreach ($comments as $comment) { ?>
                            <li class="list-group-item">
                                <div class="list-group-item-action border-warning">
                                    <div class="row">
                                        <div class="col-sm mb-2 mb-sm-0">
                                            <h4 class="fw-normal mb-1"><?php echo $comment['author'] ?> | <?php echo $comment['created_at'] ?></h4>
                                            <div class="text-inherit mb-0"><?php echo $comment['comment'] ?></div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>

        </div>

    </div>
</main>

<!-- Modal -->
<div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
        <form id="commentAdd">
            <div class="modal-body">
                <textarea name="comment" class="form-control" placeholder="Текст" id="invoiceAddressToLabel" rows="3"></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-bs-dismiss="modal">Отмена</button>
                <button type="submit" class="btn btn-primary">Добавить</button>
            </div>
        </form>
    </div>
  </div>
</div>
<!-- End Modal -->

<?php include BASE_PATH_ADMIN . '/includes/footer.php'; ?>

<script>
    const ModalInstance = new bootstrap.Modal(document.getElementById('commentModal'));
    let commentAdd = document.querySelector('#commentAdd');
    let commentList = document.querySelector('#commentList');

    commentAdd.addEventListener('submit', (event) => {
        event.preventDefault();

        const formData = new FormData(commentAdd);
        formData.append("torg_id", <?php echo $torg_id ?>);
        formData.append("author", '<?php echo $_SESSION['user_name'] ?>');

        sendData(formData, 'addComment');
    });

    async function sendData(data, meth) {
        try {
            const response = await fetch(`inwork/${meth}.php`, {
                method: "POST",
                body: data,
                headers: {
                    'X-Requested-With' : 'XMLHttpRequest'
                }
            });
            const result = await response.json();
            if(!result.error) {
                ModalInstance.hide();

                let listItem = document.createElement("li");
                listItem.classList.add('list-group-item');
                listItem.innerHTML = `
                    <div class="list-group-item-action border-warning">
                        <div class="row">
                            <div class="col-sm mb-2 mb-sm-0">
                                <h4 class="fw-normal mb-1">${result.author} | ${new Date().toLocaleString()}</h4>
                                <div class="text-inherit mb-0">${result.comment}</div>
                            </div>
                        </div>
                    </div>
                `
                commentList.append(listItem);
                
            } else {
                alert(result?.error);
            }
        } catch (error) {
            console.error('Ошибка:', error);
        }
    }
</script>