<?php
require '../vendor/autoload.php';
session_start();
require_once '../config.php';
require_once BASE_PATH_ADMIN . '/includes/auth_validate.php';

$db = getDbInstance();

$inn = filter_input(INPUT_GET, 'inn');
$ogrn = filter_input(INPUT_GET, 'ogrn');

header('Content-Type: application/json; charset=utf-8');

if(!empty($inn) || !empty($ogrn)){
    !empty($inn) ? $db->where("inn", $inn) : $db->where("ogrn", $ogrn);
    $debtor = $db->getOne("debtors");

    if($debtor) {
        if(empty($debtor['data'])){
            $debtor_api = getData($inn, $db, $ogrn);
        } else {
            $debtor_api = $debtor['data'];
        }
    
        if(empty($debtor['finansi'])) {
            $inn = !empty($inn) ? $inn : json_decode($debtor_api, true)['data']['ИНН'];
            $finansi = finansi($inn, $db);
        }

        if(empty($debtor['bo_nalog'])) {
            $inn = !empty($inn) ? $inn : json_decode($debtor_api, true)['data']['ИНН'];
            $bo_nalog = boNalog($inn, $db);
        }
    } else {
        $NewDebtor = createNewDebtor($inn, $ogrn, $db);
    }
    
    $result = !empty($NewDebtor) ? $NewDebtor : [
        'data'     => !empty($debtor['data']) ? json_decode($debtor['data'], true)['data'] : json_decode($debtor_api, true)['data'],
        'finansi'  => !empty($finansi) ? $finansi[0]['data'] : $debtor['finansi'],
        'bo_nalog' => !empty($debtor['bo_nalog']) ? $debtor['bo_nalog'] : $bo_nalog
    ];

    print_r(json_encode($result));

}


function boNalog($inn, $db)  {
    $debtor_nalog = file_get_contents('https://bo.nalog.ru/nbo/organizations/search?query='.$inn);
    $debtor_nalog = json_decode($debtor_nalog, true);
    if(!empty($debtor_nalog['content'][0])) {
        $db->where("inn", $inn);
        $db->update('debtors', ['bo_nalog' => json_encode($debtor_nalog['content'][0])]);
        return json_encode($debtor_nalog['content'][0]);
    }
}

function finansi($inn, $db) {
    $finansi = file_get_contents('http://149.154.66.33/fin.php?inn='.$inn);
    $finansi = json_decode($finansi, true);
    if(!empty($finansi[0]['data'])) {
        $db->where("inn", $inn);
        $db->update('debtors', ['finansi' => $finansi[0]['data']]);
    }
    return $finansi;
}

function getData($inn, $db, $ogrn = false) {
    if($ogrn) {
        $debtor_api = file_get_contents('https://api.ofdata.ru/v2/company?key=Gs1efsCzUftSUTO4&ogrn='.$ogrn);
        if(!empty($debtor_api)){
            $db->where("ogrn", $ogrn);
            $db->update('debtors', ['data' => $debtor_api]);
        }
    } else {
        $debtor_api = file_get_contents('https://api.ofdata.ru/v2/company?key=Gs1efsCzUftSUTO4&inn='.$inn);
        if(!empty($debtor_api)){
            $db->where("inn", $inn);
            $db->update('debtors', ['data' => $debtor_api]);
        }
    }
    return $debtor_api;
}

function createNewDebtor($inn = false, $ogrn = false, $db) {
    if($inn) {
        $id = $db->insert ('debtors', [ 'inn' => $inn ]);
    } else if($ogrn) {
        $id = $db->insert ('debtors', [ 'ogrn' => $ogrn ]);
    } else {
        return false;
    }

    $debtor_api = getData($inn, $db, $ogrn);
    $debtor_api_decode = json_decode($debtor_api, true)['data'];
    if(!empty($debtor_api_decode)) {
        $finansi = finansi($debtor_api_decode['ИНН'], $db);
        boNalog($debtor_api_decode['ИНН'], $db);
        $db->where("id", $id);
        $db->update('debtors', ['name' => $debtor_api_decode['НаимСокр'], 'ogrn' => $debtor_api_decode['ОГРН'], 'region' => $debtor_api_decode['Регион']['Наим']]);
    }

    return [
        'data' => !empty($debtor_api_decode) ? $debtor_api_decode : null,
        'finansi' => !empty($finansi) ? $finansi[0]['data'] : null
    ];
}