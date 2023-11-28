<?php
require 'vendor/autoload.php';
session_start();
require_once 'config.php';
require_once BASE_PATH_ADMIN . '/includes/auth_validate.php';
require_once BASE_PATH_ADMIN . '/helpers/helpers.php';

$db = getDbInstance();

updateInns($db);

function setStatus($db) {
    $db->pageLimit = 20;

    $db->where("status", NULL, 'IS');
    $rows = $db->arraybuilder()->paginate('debtors', 10);

    foreach($rows as $row) {
        $status = getCompBfoStatus($row['inn']);
        if($status){
            $db->where ('id', $row['id']);
            $db->update ('debtors', ['status' => $status]);
        }
    }
}

function updateInns($db) {
    $db->pageLimit = 50;
    $db->orderBy("id", "Desc");
    // $db->where("inns_list", NULL, 'IS');
    $rows = $db->arraybuilder()->paginate('torgi', 11);
    
    foreach($rows as $row) {
        $allStatuses = NULL;
        if(!empty($row['text'])) {
            preg_match_all('#(?<!\d)\d{10}(?!\d)#', $row['text'], $find_inn);
            foreach(array_unique($find_inn[0]) as $inn) {
                $get_status = getCompBfoStatus($inn);
                if($get_status) {
                    $allStatuses[] = $get_status;
                }
            }
        }        

        $db->where('id', $row['id']);
        $db->update ('torgi', ['inns_list' => !empty($allStatuses) ? json_encode($allStatuses) : NULL]);

        unset($allStatuses);
    }
}

function getCompBfoStatus($inn) {
    if(!empty($inn)){
        $debtor_nalog = file_get_contents('https://bo.nalog.ru/nbo/organizations/search?query='.($inn ? $inn : ''));
        $debtor_nalog = json_decode($debtor_nalog, true);
        if(!empty($debtor_nalog['content'][0]['statusCode'])) {
            return $debtor_nalog['content'][0]['statusCode'];
        }
    }
    return false;
}

// function getCompBfoStatus($inn, $ogrn = false) {
//     if(!empty($inn) || !empty($ogrn)){
//         $debtor_nalog = file_get_contents('https://bo.nalog.ru/nbo/organizations/search?query='.($inn ? $inn : ($ogrn ? $ogrn : '')));
//         $debtor_nalog = json_decode($debtor_nalog, true);
//         if(!empty($debtor_nalog['content'][0]['statusCode'])) {
//             return $debtor_nalog['content'][0]['statusCode'];
//         }
//     }
//     return false;
// }