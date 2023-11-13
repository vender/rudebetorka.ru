<?php
session_start();
require_once '../../config.php';
require_once '../../includes/auth_validate.php';

$db = getDbInstance();

// $numDebtors = $db->getValue("debtors", "count(*)");
// $numTorgi = $db->getValue("torgi", "count(*)");
// $stats = $db->getOne("reports", "sum(childrens) as childrens, sum(teenager) as teenager, count(*) as reps");

$statuses = $db->get('statuses');

include_once(BASE_PATH . '/includes/header.php');
?>

<main id="content" class="main">
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">Статусы лотов</h1>
                </div>

                <div class="col-sm-auto">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                        <i class="bi-plus me-1"></i> Добавить
                    </button>
                </div>
            </div>
            <!-- End Row -->
        </div>

        <div class="card">

            <!-- Table -->
            <div class="table-responsive datatable-custom">
                <table id="datatable" class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>Название</th>
                            <th>Цвет</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach($statuses as $status) { ?>
                            <tr>
                                <td><?php echo $status['name'] ?></td>
                                <td><div style="width: 30px; height: 30px;background-color: <?php echo $status['color'] ?>;"></div></td>
                                <td>
                                        <div class="d-grid gap-2 d-md-block">
                                            <a href="#" class="btn btn-primary" data-bs-original-title="Изменить" aria-label="Изменить" data-bs-toggle="modal" data-bs-target="#createModal" data-bs-rowData='{"id": "<?php echo $status['id'] ?>", "name" : "<?php echo $status['name'] ?>", "color" : "<?php echo $status['color'] ?>"}'>
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <?php if($status['id'] != 1) { ?>
                                                <button type="button" title="" class="btn btn-danger" data-bs-toggle="modal" data-bs-rowid="<?php echo $status['id'] ?>" data-bs-target="#confirm-delete" data-bs-original-title="Удалить" aria-label="Удалить"><i class="bi bi-trash"></i></button>
                                            <?php } ?>
                                        </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <!-- End Table -->

        </div>

    </div><!-- container-fluid -->

</main><!-- /#page-wrapper -->

<!-- Modal Delete -->
<div id="confirm-delete" class="modal fade" tabindex="-1" aria-labelledby="confirm-delete" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="justify-content: center;" role="document">
        <form action="/settings/statuses/delete.php" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Подтверждение удаления</h5>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="del_id" id="del_id" value="">
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
<!-- Modal Delete End -->

<!-- Create New Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <!-- Header -->
            <div class="modal-header">
                <h4 class="modal-title" id="createModalLabel">Добавить статус</h4>
                <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
            </div>
            <!-- End Header -->
            
            <!-- Form -->
            <form id="createModalForm">

                <!-- Body -->
                <div class="modal-body">
                        <div class="input-group input-group-md-vertical">
                            <input id="rowName" type="text" name="name" class="form-control" placeholder="Имя" require>
                            <input id="rowColor" type="color" name="color" class="form-control" style="height: 50px;" placeholder="Цвет" require>
                        </div>
                </div>
                <!-- End Body -->

                <!-- Footer -->
                <div class="modal-footer">
                    <div class="row align-items-sm-center flex-grow-1 mx-n2">

                        <div class="col-sm-auto">
                            <div class="d-flex gap-3">
                                <button type="button" class="btn btn-white" data-bs-dismiss="modal" aria-label="Close">Отмена</button>
                                <button type="submit" class="btn btn-primary">Сохранить</button>
                            </div>
                        </div>
                        <!-- End Col -->
                    </div>
                    <!-- End Row -->
                </div>
                <!-- End Footer -->

            </form>
            <!-- End Form -->
        </div>
    </div>
</div>
<!-- End Create New API Key Modal -->

<?php include_once(BASE_PATH . '/includes/footer.php'); ?>

<script>
    const createModalForm = document.getElementById('createModalForm');
    const confirmDelete = document.getElementById('confirm-delete');
    const Modal = document.getElementById('createModal');
    const ModalInstance = new bootstrap.Modal(Modal);
    let editMode = false;
    let rowData;

    Modal && Modal.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget;
        rowData = JSON.parse(button.getAttribute('data-bs-rowData'));

        if(rowData) {
            const modalTitle = Modal.querySelector('.modal-title');
            const modalRowName = Modal.querySelector('.modal-body #rowName');
            const modalRowColor = Modal.querySelector('.modal-body #rowColor');

            modalTitle.textContent = `Изменить статус`
            modalRowName.value = rowData?.name;
            modalRowColor.value = rowData?.color;

            editMode = true;
        }
    });

    confirmDelete && confirmDelete.addEventListener('show.bs.modal', event => {
        const rowId = event.relatedTarget.getAttribute('data-bs-rowid');
        confirmDelete.querySelector('#del_id').value = rowId;
    });

    createModalForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(createModalForm);
        rowData && formData.append('id', rowData?.id);
        editMode && rowData ? sendData(formData, 'edit') : sendData(formData, 'create');
    });

    async function sendData(data, method) {
        try {
            const response = await fetch(`/settings/statuses/${method}.php`, {
                method: "POST",
                body: data,
                headers: {
                    'X-Requested-With' : 'XMLHttpRequest'
                }
            });
            const result = await response.json();
            if(!result.error) {
                ModalInstance.hide();
                setTimeout(() => {location.reload()}, 400);
            } else {
                alert(result?.error);
            }
        } catch (error) {
            console.error('Ошибка:', error);
        }
    }
</script>