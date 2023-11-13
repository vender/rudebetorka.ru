<?php
session_start();
require_once '../config.php';
require_once BASE_PATH_ADMIN . '/includes/auth_validate.php';

//Get DB instance. i.e instance of MYSQLiDB Library
$db = getDbInstance();

$statuses = $db->get('statuses');

include BASE_PATH_ADMIN . '/includes/header.php';
?>
<!-- Main container -->
<main id="content" class="main">
    <div class="content container-fluid">

        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                <h1 class="h2">Лоты в работе</h1>
            </div>
        </div>

        <?php include BASE_PATH_ADMIN . '/includes/flash_messages.php'; ?>


        <div class="tab-content" id="connectionsTabContent">
            <div class="tab-pane fade show active" id="grid" role="tabpanel" aria-labelledby="grid-tab">
                <div class="content container-fluid kanban-board">
                    <!-- Kanban Row -->
                    <ul class="row list-unstyled kanban-board-row">
                        <?php 
                            foreach($statuses as $status) { 
                            //If order by option selected
                            $db->orderBy('id', 'Desc');
                            // Get result of the query.
                            $db->where('inwork', 'true');
                            $db->where('status_id', $status['id']);
                            $rows = $db->get('torgi');
                        ?>
                            <li class="js-add-field col-12 border-end">
                                <!-- Title -->
                                <div class="js-sortable-disabled d-flex justify-content-between align-items-center mb-3" style="border-bottom: 3px solid <?php echo $status['color'] ?>;">
                                    <h6 class="text-cap mb-0"><?php echo $status['name'] ?></h6>
                                </div>
                                <!-- End Title -->

                                <div class="js-sortable h-100" data-statusid="<?php echo $status['id'] ?>">
                                    
                                    <?php foreach ($rows as $row) : ?>
                                            <!-- Card -->
                                            <div id="row-<?php echo $row['id'] ?>" class="js-sortable-link sortablejs-custom sortablejs-custom-rotate sortablejs-custom-handle <?php echo $row['lock_status'] == 'true' ? 'filtered' : '' ?>" data-torgid="<?php echo $row['id'] ?>">
                                                <div class="card mb-3">
                                                    <div class="card-body">
                                                        <div class="d-flex mb-2">
                                                            <i class="bi bi-grip-vertical sorthandle"></i>
                                                            <div class="me-2 text-wrap text-dark fw-semibold">
                                                                <a href="/inwork/detailed.php?torg_id=<?php echo $row['id'] ?>"><?php echo $row['title'] ?></a>
                                                            </div>

                                                            <div class="ms-auto d-flex">
                                                                <!-- Dropdown -->
                                                                <div class="dropdown">
                                                                    <button type="button" class="btn btn-ghost-secondary btn-icon btn-xs card-dropdown-btn rounded-circle" id="kanbanProjectsGridDropdown6" data-bs-toggle="dropdown" aria-expanded="false">
                                                                        <i class="bi bi-three-dots-vertical"></i>
                                                                    </button>

                                                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="kanbanProjectsGridDropdown6">
                                                                        <a class="dropdown-item" href="#">
                                                                            <i class="bi-pencil dropdown-item-icon"></i> 1
                                                                        </a>
                                                                        <a class="dropdown-item" href="#">
                                                                            <i class="bi-star dropdown-item-icon"></i> 2
                                                                        </a>
                                                                        <a class="dropdown-item" href="#">
                                                                            <i class="bi-archive dropdown-item-icon"></i> 3
                                                                        </a>

                                                                        <div class="dropdown-divider"></div>

                                                                        <a class="dropdown-item text-danger" href="#">
                                                                            <i class="bi-trash dropdown-item-icon text-danger"></i>
                                                                            Удалить
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                                <!-- End Dropdown -->

                                                                <div class="lock-status ms-auto">
                                                                    <div class="form-check form-check-switch">
                                                                        <input class="form-check-input" type="checkbox" value="" id="Checkbox<?php echo $row['id'] ?>" <?php echo $row['lock_status'] == 'true' ? 'checked' : '' ?> data-torgid="<?php echo $row['id'] ?>" >
                                                                        <label class="form-check-label btn-icon btn-xs rounded-circle" <?php echo $_SESSION['admin_type'] == 'super' ? 'for="Checkbox'.$row['id'].'" onclick="setLockStatus(this)"' : ''; ?>>
                                                                            <span class="form-check-active" data-bs-toggle="tooltip" data-bs-placement="top" title="Заблокирован">
                                                                                <i class="bi bi-lock-fill"></i>
                                                                            </span>
                                                                            <span class="form-check-default" data-bs-toggle="tooltip" data-bs-placement="top" title="Разблокирован">
                                                                                <i class="bi bi-unlock-fill"></i>
                                                                            </span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>

                                                        <!-- <div class="my-2">
                                                            <?php //echo $row['text'] ?>
                                                        </div> -->
                                                        <!-- End Row -->

                                                    </div>
                                                </div>
                                            </div>
                                            <!-- End Card -->
                                        <?php endforeach; ?>
                                    
                                </div>

                                
                            </li>
                        <?php } ?>

                    </ul>
                </div>
            </div>
        </div>


    </div>
</main>
<!-- //Main container -->
<?php include BASE_PATH_ADMIN . '/includes/footer.php'; ?>

<script>
    // INITIALIZATION OF SORTABLEJS
    // =======================================================
    HSCore.components.HSSortable.init('.js-sortable', {
        forceFallback: true,
        filter: '.filtered',
        handle: '.sorthandle',
        animation: 150,
        group: 'listGroup',
        delay: 500,
        delayOnTouchOnly: true,
        onEnd: function (evt) {
            const torgID = evt.item.getAttribute('data-torgid') ? evt.item.getAttribute('data-torgid') : false;
            const status_id = evt.item.getAttribute('data-torgid') ? evt.item.getAttribute('data-torgid') : false;
            if(evt.from?.dataset?.statusid != evt.to?.dataset?.statusid && torgID) {
                // console.log(evt);
                const formData = new FormData();
                formData.append("id", torgID);
                formData.append("status_id", evt.to?.dataset?.statusid);
                sendData(formData, 'setStatus');
            }
        },
    });

    function setLockStatus(el) {
        if(el?.control) {
            const torgID = el?.control.getAttribute('data-torgid') ? el?.control.getAttribute('data-torgid') : false;
            const status = el?.control?.checked ? 'false' : 'true';
            const rowNod = document.querySelector(`#row-${torgID}`);
            rowNod.classList.contains('filtered') ? rowNod.classList.remove('filtered') : rowNod.classList.add('filtered');
            const formData = new FormData();
            formData.append("id", torgID);
            formData.append("lock_status", status);
            sendData(formData, 'setLockStatus');
        }
    }

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
                return result;
            } else {
                alert(result?.error);
            }
        } catch (error) {
            console.error('Ошибка:', error);
        }
    }
</script>