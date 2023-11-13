<?php
require 'vendor/autoload.php';
session_start();
require_once 'config.php';
require_once BASE_PATH_ADMIN . '/includes/auth_validate.php';

//Get DB instance. i.e instance of MYSQLiDB Library
$db = getDbInstance();

$inn = filter_input(INPUT_GET, 'inn');

header('Content-Type: application/json; charset=utf-8');

$debtor_nalog = file_get_contents('http://149.154.66.33/fin.php?inn='.$inn);
$debtor_nalog = json_decode($debtor_nalog, true);

// foreach(json_decode($debtor_nalog[0]['data'], true) as $year) {
//     print_r($year);
// }

print_r($debtor_nalog[0]['data']);