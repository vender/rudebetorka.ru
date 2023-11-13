<?php
require '../vendor/autoload.php';
session_start();
require_once '../config.php';
require_once BASE_PATH_ADMIN . '/includes/auth_validate.php';

$db = getDbInstance();

$inn = filter_input(INPUT_GET, 'inn');

if(!empty($inn)){
    header('Content-Type: application/json; charset=utf-8');

    $db->where("inn", $inn);
    $debtor = $db->getOne("debtors");

    if(empty($debtor['bo_nalog'])) {
        $debtor_nalog = file_get_contents('https://bo.nalog.ru/nbo/organizations/search?query='.$inn);
        $debtor_nalog = json_decode($debtor_nalog, true);
        if(!empty($debtor_nalog['content'][0])) {
            $db->where("inn", $inn);
            $db->update('debtors', ['bo_nalog' => json_encode($debtor_nalog['content'][0])]);
        }
    }

    if(empty($debtor['finansi'])) {
        $finansi = file_get_contents('http://149.154.66.33/fin.php?inn='.$inn);
        $finansi = json_decode($finansi, true);
        if(!empty($finansi[0]['data'])) {
            $db->where("inn", $inn);
            $db->update('debtors', ['finansi' => $finansi[0]['data']]);
        }
    }

    if(empty($debtor['data'])){
        $debtor_api = file_get_contents('https://api.ofdata.ru/v2/company?key=Gs1efsCzUftSUTO4&inn='.$inn);
        if(!empty($debtor_api)){
            $db->where("inn", $inn);
            $db->update('debtors', ['data' => $debtor_api]);
        }
    }

    $result = [
        'data' => !empty($debtor['data']) ? json_decode($debtor['data'], true)['data'] : json_decode($debtor_api, true)['data'],
        'finansi' => !empty($finansi) ? $finansi[0]['data'] : $debtor['finansi']
    ];
    
    print_r(json_encode($result));
}