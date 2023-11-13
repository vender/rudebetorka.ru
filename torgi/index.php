<?php
require '../vendor/autoload.php';
session_start();
require_once '../config.php';
require_once BASE_PATH_ADMIN . '/includes/auth_validate.php';

//Get DB instance. i.e instance of MYSQLiDB Library
$db = getDbInstance();

// Per page limit for pagination.
$pagelimit = 20;

// Get current page.
$page = filter_input(INPUT_GET, 'page');
if (!$page) {
    $page = 1;
}

$select = array('t.id', 't.number', 't.title', 't.text', 't.sum', 't.step', 't.deposit', 't.debtor_id', 't.lot_created', 't.created_at', 'd.name', 'd.inn', 'd.bo_nalog');
$db->join("debtors d", "t.debtor_id=d.debtor_id", "LEFT");

// Set pagination limit
$db->pageLimit = $pagelimit;
// Get result of the query.
$db->orderBy("id", "Desc");
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
                        <th width="40%">Описание</th>
                        <th width="15%">Цены</th>
                        <th width="15%">Активы</th>
                        <th width="30%">Должник</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row) : ?>
                        <tr>
                            <!-- <td>
                                
                            </td> -->
                            <td class="text-truncate" style="white-space: normal; max-width: 20%;">
                                <a href="https://tbankrot.ru/item?id=<?php echo $row['number'] ?>" class="icon-link" target="_blank" rel="noopener noreferrer">
                                    <?php echo $row['title'] ?> <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                                <br>
                                <?php echo $row['text']; ?>
                            </td>
                            <td>
                                <span class="badge bg-dark" data-bs-toggle="tooltip" data-bs-html="true" title="Стартовая"><?php echo $row['sum']; ?> руб</span><br>
                                <span class="badge bg-success" data-bs-toggle="tooltip" data-bs-html="true" title="Текущая"><?php echo $row['step']; ?></span><br>
                                <span class="badge bg-danger" data-bs-toggle="tooltip" data-bs-html="true" title="Минимальная"><?php echo $row['deposit']; ?></span>
                            </td>
                            <td>
                                <?php if(!empty($row['bo_nalog'])) { $bo_nalog = json_decode($row['bo_nalog'], true); ?>
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
                            <td style="white-space: initial;">
                                <?php if(!empty($row['inn'])) { ?>
                                    <mark id="<?php echo $row['inn']; ?>" style="cursor: pointer;" data-bs-toggle="modal" data-bs-inn="<?php echo $row['inn']; ?>" data-bs-target="#editUserModal"><?php echo $row['name']; ?></mark>
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


<!-- Modal -->
<div id="editUserModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editUserModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Nav -->
                <div class="text-center">
                    <ul class="nav nav-segment nav-pills mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="nav-one-eg1-tab" href="#nav-one-eg1" data-bs-toggle="pill" data-bs-target="#nav-one-eg1" role="tab" aria-controls="nav-one-eg1" aria-selected="true">Общее</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="nav-two-eg1-tab" href="#nav-two-eg1" data-bs-toggle="pill" data-bs-target="#nav-two-eg1" role="tab" aria-controls="nav-two-eg1" aria-selected="false">Связанные организации</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="nav-three-eg1-tab" href="#nav-three-eg1" data-bs-toggle="pill" data-bs-target="#nav-three-eg1" role="tab" aria-controls="nav-three-eg1" aria-selected="false">Финансы</a>
                        </li>
                    </ul>
                </div>
                <!-- End Nav -->

                <!-- Tab Content -->
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="nav-one-eg1" role="tabpanel" aria-labelledby="nav-one-eg1-tab">
                        <div id="comp_result">
                            <div class="d-flex justify-content-center">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Загрузка...</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="nav-two-eg1" role="tabpanel" aria-labelledby="nav-two-eg1-tab">
                        <div id="comp_relations">
                            <div class="d-flex justify-content-center">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Загрузка...</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="nav-three-eg1" role="tabpanel" aria-labelledby="nav-three-eg1-tab">
                        <div id="finansi">
                            <div class="d-flex justify-content-center">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Загрузка...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Tab Content -->

            </div>
        </div>
    </div>
</div>
<!-- End Modal -->


<?php include BASE_PATH_ADMIN . '/includes/footer.php'; ?>

<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('title'))
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Modal Events
    const editUserModal = document.getElementById('editUserModal');
    editUserModal.addEventListener('show.bs.modal', async (event) => {
        const button = event.relatedTarget;
        const inn = button.getAttribute('data-bs-inn');
        LoadingState("#comp_result");
        const res = await GetCompData(inn);
        const debtor = res.data;
        let finansi = JSON.parse(res.finansi);
        finansi = Object.entries(finansi).filter(item => item[0] >= 2015);
        finansi = Object.fromEntries(finansi);

        debtorInfo(debtor);
        debtorFinanse(finansi);
    });


    async function GetCompData(inn) {
        let url = `/helpers/api.php?inn=${inn}`;
        const res = await fetch(url);
        const json = await res.json();
        return json;
    }

    function LoadingState(selector) {
        editUserModal.querySelector(selector).innerHTML = `
            <div class="d-flex justify-content-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Загрузка...</span>
                </div>
            </div>
        `;
    }

    function debtorInfo(debtor) {

        editUserModal.querySelector('.modal-title').innerHTML = debtor.НаимСокр;
        editUserModal.querySelector("#comp_result").innerHTML = `
            <div class="accordion" id="accordion">
                <div class="accordion-item">
                    <div class="accordion-header" id="heading">
                        <a class="accordion-button bg-light" role="button" data-bs-toggle="collapse" data-bs-target="#about" aria-expanded="true" aria-controls="about">О должнике</a>
                    </div>
                    <div id="about" class="accordion-collapse collapse show" aria-labelledby="heading" data-bs-parent="#accordion">
                        <div class="accordion-body">
                            <ul class="list-unstyled list-py-2 text-dark mt-3 mb-0">
                                <li><b>ИНН:</b> ${debtor.ИНН}</li>
                                <li><b>ОГРН:</b> ${debtor.ОГРН}</li>
                                <li><b>Статус:</b> ${debtor?.Статус?.Наим}</li>
                                <li><b>Уставный капитал:</b> ${debtor?.УстКап?.Сумма}</li>
                                <li><b>ОКВЭД:</b> ${debtor?.ОКВЭД?.Наим} (${debtor?.ОКВЭД?.Код})</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <div class="accordion-header" id="heading">
                        <a class="accordion-button bg-light collapsed" role="button" data-bs-toggle="collapse" data-bs-target="#contacts" aria-expanded="false" aria-controls="contacts">Контакты</a>
                    </div>
                    <div id="contacts" class="accordion-collapse collapse" aria-labelledby="heading" data-bs-parent="#accordion">
                        <div class="accordion-body">
                            <ul class="list-unstyled list-py-2 text-dark mt-3 mb-0">
                                <li><b>ВебСайт:</b> ${debtor?.Контакты?.ВебСайт}</li>
                                <li><b>Телефоны:</b> ${debtor?.Контакты?.Тел?.map(item=>item)}</li>
                                <li><b>Адрес:</b> ${debtor.ЮрАдрес.АдресРФ}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <div class="accordion-header" id="heading">
                        <a class="accordion-button bg-light collapsed" role="button" data-bs-toggle="collapse" data-bs-target="#managers" aria-expanded="false" aria-controls="managers">Руководство</a>
                    </div>
                    <div id="managers" class="accordion-collapse collapse" aria-labelledby="heading" data-bs-parent="#accordion">
                        <div class="accordion-body">
                            <ul class="list-unstyled list-py-2 text-dark mt-3 mb-0">
                                ${debtor?.Руковод?.map(item=> `<li><b>${item?.НаимДолжн}:</b> ${item?.ФИО}(ИНН: ${item?.ИНН})</li>`)}
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <div class="accordion-header" id="heading">
                        <a class="accordion-button bg-light collapsed" role="button" data-bs-toggle="collapse" data-bs-target="#founders" aria-expanded="false" aria-controls="founders">Учредители</a>
                    </div>
                    <div id="founders" class="accordion-collapse collapse" aria-labelledby="heading" data-bs-parent="#accordion">
                        <div class="accordion-body">
                            <ul class="list-group list-group-flush list-group-start-bordered mt-3 mb-0">
                                ${debtor?.Учред?.ФЛ && debtor?.Учред?.ФЛ?.map(item => {
                                    return `
                                        <li class="list-group-item">
                                            <div class="list-group-item-action border-primary" href="#">
                                                <div class="row">
                                                    <div class="col-sm mb-2 mb-sm-0">
                                                        <h3 class="fw-normal mb-1">${item?.ФИО}</h3>
                                                        <h4 class="text-inherit">ИНН: ${item?.ИНН}</h4>
                                                        <h4 class="text-body">Доля: ${item?.Доля?.Номинал} (${item?.Доля?.Процент}%)</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    `;
                                }).join(' ')}
                                
                                ${debtor?.Учред?.РФ && debtor?.Учред?.РФ?.map(item => {
                                    return `
                                        <li class="list-group-item">
                                            <div class="list-group-item-action border-primary" href="#">
                                                <div class="row">
                                                    <div class="col-sm mb-2 mb-sm-0">
                                                        <h3 class="fw-normal mb-1">${item?.НаимМО}</h3>
                                                        <h4 class="text-inherit">ИНН: ${item?.ОргОсущПрав[0].ИНН}</h4>
                                                        <h4 class="text-body">Доля: ${item?.Доля?.Номинал} (${item?.Доля?.Процент}%)</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    `;
                                }).join(' ')}
                                
                                ${debtor?.Учред?.РосОрг && debtor?.Учред?.РосОрг?.map(item => {
                                    return `
                                        <li class="list-group-item">
                                            <div class="list-group-item-action border-primary" href="#">
                                                <div class="row">
                                                    <div class="col-sm mb-2 mb-sm-0">
                                                        <h3 class="fw-normal mb-1">${item?.НаимСокр}</h3>
                                                        <h4 class="text-inherit">ИНН: ${item?.ИНН}</h4>
                                                        <h4 class="text-body">Доля: ${item?.Доля?.Номинал} (${item?.Доля?.Процент}%)</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    `;
                                }).join(' ')}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    async function debtorFinanse(finansi) {
        let accCodes = await GetCodes();
        let trows;

        trows = accCodes.map((code, cidx) => {
            if(code.code < 1999) {
                return `
                    ${`
                        <tr>
                            <td>${code.name} (${code.code})</td>
                            ${Object.entries(finansi).map((year, index) => {
                                return year[1][code?.code] ? `<td>${new Intl.NumberFormat("ru-RU").format(year[1][code?.code])}</td>` : `<td></td>`;
                            }).join(' ')}
                        </tr>
                    `}
                `
            };
        }).join(' ')

        let finTable = `
            <div class="row justify-content-between align-items-center flex-grow-1">
                <div class="col-md">
                    <h6 class="card-header-title">Все суммы указаны в тысячах рублей</h6>
                </div>
                <div class="col-auto">
                    <div class="dropdown">
                        <button class="btn btn-ghost-secondary" type="button" id="dropdownMenuButtonGhostPrimary"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-table"></i>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButtonGhostPrimary">
                            ${Object.entries(finansi).map((year, index) => {
                                return `
                                    <div class="d-flex align-items-center justify-content-between form-check form-switch form-switch-between mb-3">
                                        <label class="form-check-label">${year[0]}</label>
                                        <input type="checkbox" id="toggleColumn_${year[0]}" class="form-check-input" checked>
                                    </div>
                                `;
                            }).join(' ')}

                        </div>
                    </div>
                </div>
            </div>
            
            <div class="js-sticky-header">
                <div class="table-responsive datatable-custom">
                    <table id="finansi-table" class="js-datatable table-sm table table-thead-bordered table-align-middle card-table" data-page-length='50' data-hs-datatables-options='{"order": [] }'>

                        <thead class="thead-light">
                            <tr>
                                <th>Наименование</th>
                                ${Object.entries(finansi).map((year) => `<th>${year[0]}</th>`).join(' ')}
                            </tr>
                        </thead>

                        <tbody>
                            ${trows}
                        </tbody>
                    </table>
                </div>
            </div>
        `

        editUserModal.querySelector("#finansi").innerHTML = finTable;
        
        HSCore.components.HSDatatables.init('.js-datatable');
        const datatableSortingColumn = HSCore.components.HSDatatables.getItem('finansi-table')

        const StickyHeader = new HSTableStickyHeader('.js-sticky-header').init();
        
        Object.entries(finansi).map((year, index) => {
            document.getElementById(`toggleColumn_${year[0]}`).addEventListener('change', function (e) {
                datatableSortingColumn.columns(index+1).visible(e.target.checked)
            })
        });

    }

    async function GetCodes() {
        const res = await fetch(`/files/account_codes.json`);
        const json = await res.json();
        return Object.values(json);
    }
    
</script>