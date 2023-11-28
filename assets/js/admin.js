document.addEventListener('DOMContentLoaded', function(){ // Аналог $(document).ready(function(){
    let modalWrapper = document.createElement("div");
    modalWrapper.innerHTML = `
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
                                <div class="accordion accordion-flush" id="comp_relations">
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
    `;
    
    document.querySelector('body').append(modalWrapper);
    
    // Modal Events
    const editUserModal = document.getElementById('editUserModal');
    editUserModal && editUserModal.addEventListener('show.bs.modal', async (event) => {
        const button = event.relatedTarget;
        const inn = button.getAttribute('data-bs-inn');
        LoadingState(editUserModal, "#comp_result");
        const res = await GetCompData(inn);
        const debtor = res.data;
        const founders = debtor?.Учред?.ИнОрг.concat(debtor?.Учред.ПИФ, debtor?.Учред.РФ, debtor?.Учред.РосОрг, debtor?.Учред.ФЛ);
        let finansi = JSON.parse(res.finansi);
        finansi = finansi && Object?.entries(finansi)?.filter(item => item[0] >= 2015);
        finansi = finansi && Object?.fromEntries(finansi);

        debtorInfo(debtor, founders);
        debtoRelations(founders);
        finansi && debtorFinanse(finansi, editUserModal);
        await getRelatedCompFinance();
    });
});

async function getRelatedCompFinance() {
    let finansiModalWrapper = document.createElement("div");
    finansiModalWrapper.innerHTML = `
        <div id="finansiModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="finansiModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="finansiModalTitle"></h5>
                        <button type="button" class="btn-close" data-bs-target="#editUserModal" data-bs-toggle="modal" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="finansi">
                            <div class="d-flex justify-content-center">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Загрузка...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    document.querySelector('body').append(finansiModalWrapper);

    // Modal Events
    const finansiModal = document.getElementById('finansiModal');
    finansiModal && finansiModal.addEventListener('show.bs.modal', async (event) => {
        const button = event.relatedTarget;
        const inn = button.getAttribute('data-bs-inn');
        LoadingState(finansiModal,"#finansiModal #finansi");
        const res = await GetCompData(inn);
        const debtor = res.data;
        let finansi = JSON.parse(res.finansi);
        finansiModal.querySelector('.modal-title').innerHTML = debtor.НаимСокр;
        finansi = finansi && Object?.entries(finansi)?.filter(item => item[0] >= 2015);
        finansi = finansi && Object?.fromEntries(finansi);
        finansi && debtorFinanse(finansi, finansiModal);
    });
}

function debtorInfo(debtor, founders) {

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
                            ${founders && founders.map(item => {
                                return `
                                    <li class="list-group-item">
                                        <div class="list-group-item-action border-primary" href="#">
                                            <div class="row">
                                                <div class="col-sm mb-2 mb-sm-0">
                                                    <h3 class="fw-normal mb-1">${item?.ФИО ? item?.ФИО : item?.НаимСокр}</h3>
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

async function debtorFinanse(finansi, modal) {
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
            ${modal.id == 'editUserModal' ? `<div class="col-auto">
                <div id="year-filter" class="dropdown">
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
            </div>` : ''}
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

    modal.querySelector("#finansi").innerHTML = finTable;
    
    if(modal.id == 'editUserModal') {
        HSCore.components.HSDatatables.init('.js-datatable');
        const datatableSortingColumn = HSCore.components.HSDatatables.getItem('finansi-table');

        Object.entries(finansi).map((year, index) => {
            document.getElementById(`toggleColumn_${year[0]}`).addEventListener('change', function (e) {
                datatableSortingColumn.columns(index+1).visible(e.target.checked)
            })
        });
    }

}

async function debtoRelations(founders) {

    let relations = founders && founders.map((item, idx) => {
        return `
            <div class="accordion-item">
                <div class="accordion-header" id="heading-${idx}">
                    <a class="accordion-button collapsed" role="button" data-bs-toggle="collapse" data-bs-target="#accordion-${idx}" aria-expanded="false" aria-controls="accordion-${idx}">
                        ${item?.ФИО ? `${item?.ФИО} &nbsp;&nbsp; <span class="badge bg-secondary">Ру - ${item?.СвязРуковод?.length}</span> &nbsp; <span class="badge bg-info">Уч - ${item?.СвязУчред?.length}</span>` : item?.НаимСокр}
                    </a>
                </div>
                <div id="accordion-${idx}" class="accordion-collapse collapse" aria-labelledby="heading-${idx}" data-bs-parent="#comp_relations">
                    <div id="inn-${item?.ИНН}" class="accordion-body">
                        <span class="divider-center">Руководитель</span>
                        <ul id="managers" class="list-group list-group-flush list-group-start-bordered"></ul>

                        <span class="divider-center">Учредитель</span>
                        <ul id="founders" class="list-group list-group-flush list-group-start-bordered"></ul>
                    </div>
                </div>
            </div>
        `
    }).join(' ');

    editUserModal.querySelector("#comp_relations").innerHTML = relations;

    founders && await renderManagers(founders);
}

async function renderManagers(founders) {
    
    founders.map(founder => {
        let managersRow = document.querySelector(`#inn-${founder.ИНН} > #managers`);
        let foundersRow = document.querySelector(`#inn-${founder.ИНН} > #founders`);
        
        founder.СвязРуковод && founder.СвязРуковод.map(async (ogrn, oidx) => {
            let res = await GetCompData(false, ogrn);
            let compData = res?.data;
            let bo_nalog = JSON.parse(res?.bo_nalog);

            const newLi = document.createElement("li");
            newLi.className = "list-group-item";
            newLi.innerHTML = `
                <div class="list-group-item-action border-secondary" href="#">
                    <div class="row">
                        <div class="col-sm mb-2 mb-sm-0">
                            <h3 class="fw-normal mb-1">${compData?.НаимСокр}</h3>
                            <h4 class="text-inherit">ИНН: <mark id="${compData?.ИНН}" style="cursor: pointer;" data-bs-toggle="modal" data-bs-inn="${compData?.ИНН}" data-bs-target="#finansiModal">${getCompStatus(bo_nalog)}${compData?.ИНН}</mark></h4>
                        </div>
                        ${bo_nalog ? `
                            <div class="col-sm mb-2 mb-sm-0">
                                <a href="https://bo.nalog.ru/organizations-card/${bo_nalog.id}" class="icon-link" target="_blank" rel="noopener noreferrer">
                                    Отчетность <i class="bi bi-box-arrow-up-right"></i>
                                </a><br>
                                ${bo_nalog.bfo.map(item => {
                                    return item.actives && `${item.period}г. - <span class="badge bg-soft-secondary text-secondary">${item.actives * 1000} ₽</span>`
                                }).join('<br>')}
                            </div>
                            ` : ``
                        }
                    </div>
                </div>
            `;
            managersRow.append(newLi);
        });
        
        founder.СвязУчред && founder.СвязУчред.map(async (ogrn, oidx) => {
            let res = await GetCompData(false, ogrn);
            let compData = res?.data;
            let bo_nalog = res?.bo_nalog && JSON.parse(res.bo_nalog);
            
            const newLi = document.createElement("li");
            newLi.className = "list-group-item";
            newLi.innerHTML = `
                <div class="list-group-item-action border-info" href="#">
                    <div class="row">
                        <div class="col-sm mb-2 mb-sm-0">
                            <h3 class="fw-normal mb-1">${compData?.НаимСокр}</h3>
                            <h4 class="text-inherit">ИНН: <mark id="${compData?.ИНН}" style="cursor: pointer;" data-bs-toggle="modal" data-bs-inn="${compData?.ИНН}" data-bs-target="#finansiModal">${getCompStatus(bo_nalog)}${compData?.ИНН}</mark></h4>
                            <h4 class="text-body"></h4>
                        </div>
                        ${bo_nalog ? `
                            <div class="col-sm mb-2 mb-sm-0">
                                <a href="https://bo.nalog.ru/organizations-card/${bo_nalog.id}" class="icon-link" target="_blank" rel="noopener noreferrer">
                                    Отчетность <i class="bi bi-box-arrow-up-right"></i>
                                </a><br>
                                ${bo_nalog.bfo.map(item => {
                                    return item.actives && `${item.period}г. - <span class="badge bg-soft-secondary text-secondary">${item.actives * 1000} ₽</span>`
                                }).join('<br>')}
                            </div>
                            ` : ``
                        }
                    </div>
                </div>
            `;
            foundersRow.append(newLi);
        });
    });
}

function getCompStatus(bo_nalog) {
    let inn_status = `<span class="legend-indicator"></span>`;
    if(bo_nalog) {
        switch (bo_nalog.statusCode) {
            case 'ACTIVE':
                inn_status = '<span class="legend-indicator bg-success"></span>';
                break;
            case 'LIQUIDATION_STAGE':
                inn_status = '<span class="legend-indicator bg-warning"></span>';
                break;
            case 'INACTIVE':
                inn_status = '<span class="legend-indicator bg-danger"></span>';
                break;
        }
    }
    return inn_status;
}

async function GetCodes() {
    const res = await fetch(`/files/account_codes.json`);
    const json = await res.json();
    return Object.values(json);
}

async function GetCompData(inn, ogrn = false) {
    let url = inn ? `/helpers/api.php?inn=${inn}` : `/helpers/api.php?ogrn=${ogrn}`;
    const res = await fetch(url);
    const json = await res.json();
    return json;
}

function LoadingState(modal, selector) {
    modal.querySelector(selector).innerHTML = `
        <div class="d-flex justify-content-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Загрузка...</span>
            </div>
        </div>
    `;
}

// const merge = (a, b, predicate = (a, b) => a === b) => {
//     const c = [...a]; // copy to avoid side effects
//     // add all items from B to copy C if they're not already present
//     b.forEach((bItem) => (c.some((cItem) => predicate(bItem, cItem)) ? null : c.push(bItem)))
//     return c;
// }