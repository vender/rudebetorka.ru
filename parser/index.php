<?php
require '../vendor/autoload.php';
include_once('simple_html_dom.php');
require_once '../config.php';
use GuzzleHttp\Client;

$db = getDbInstance();

$json = file_get_contents('user-agents.json');
$userAgents = json_decode($json);

$client = new Client([
    // Base URI is used with relative requests
    'base_uri' => 'https://tbankrot.ru',
    // You can set any number of default request options.
    // 'timeout'  => 2.0,
    'cookies' => true,
    'headers' => [
        'User-Agent' => $userAgents[rand(0, count($userAgents) - 1)],
    ],
    // 'proxy' => 'https://91.238.211.110:8080'
]);

$response = $client->request('POST', '/script/submit.php', [
    'form_params' => [
        'key' => 'login',
        'mail' => 'Syromyatnik0v@mail.ru',
        'pas' => 'Sve0110Zz'
    ],
    'verify' => false
]);

$response = $client->request('POST', '/script/submit.php', [
    'form_params' => [
        'key' => 'pageitemcount',
        'count' => 100,
        'page' => ''
    ],
    'verify' => false
]);

$response = $client->request('GET', '/monitoring', [
    'verify' => false,
]);


$document = (string) $response->getBody();

$html = str_get_html($document);

foreach($html->find('div.torg') as $l) {
    $number = $l->getAttribute('data-rel');
    $title = $l->find('.lot_info .main_info .info_head .num a', 0);
    $text = $l->find('.lot_info .main_info .info_body ', 0);
    $sum = $l->find('.lot_info .main_info .price_info .sum', 0);
    $step = $l->find('.lot_info .main_info .price_info .cur_price .green', 0);
    $deposit = $l->find('.lot_info .main_info .price_info .cur_price .red', 0);
    $debtor = $l->find('.lot_info .debtor a', 0);
    $debtor_id = explode('=', $debtor ? $debtor->getAttribute('href') : '');
    $lot_created = $l->find('.lot_info .main_info .price_info .created', 0);
    if($l->find('.lot_info .notesBlock.dop_info_3 .dates tr', 0)) {
        $dates_from = $l->find('.lot_info .notesBlock.dop_info_3 .dates tr', 0)->find('td',0);
        $dates_torg = $l->find('.lot_info .notesBlock.dop_info_3 .dates tr', 1)->find('td',0);
    } else {
        $dates_from = '';
        $dates_torg = '';
    }
    
    $data_to_store = [
        'number'    => $number ? $number : false,
        'title'     => $title ? trim($title->plaintext) : '',
        'text'      => $text ? trim(str_replace('Показать всё описание...', '', $text->plaintext)) : '',
        'sum'       => $sum ? $sum->plaintext : '',
        'step'      => $step ? $step->plaintext : '',
        'deposit'   => $deposit ? $deposit->plaintext : '',
        'debtor_id' => $debtor ? end($debtor_id) : null,
        'lot_created' => $lot_created ? $lot_created->plaintext : '',
        'dates_from' => $dates_from ? preg_replace("/  +/", '', $dates_from->innertext) : '',
        'dates_torg' => $dates_torg ? preg_replace("/  +/", '', $dates_torg->innertext) : ''
    ];

    $data_to_store['inns_list'] = searchInnsInText($data_to_store['text']);
    // print_r($data_to_store['inns_list']);

    if(!empty($number)) {
        $db->where ("number", $number);
        $lot = $db->getOne ("torgi");
        if(!$lot) $last_id = $db->insert ('torgi', $data_to_store);
    }

    $db->where("debtor_id", $data_to_store['debtor_id']);
    $debtor_row = $db->getOne("debtors");

    if(!empty($debtor) && empty($debtor_row)) {
        // $debtor_page = $client->request('GET', $debtor->getAttribute('href'), [
        //     'verify' => false,
        // ]);

        $debtor_html = file_get_html('https://tbankrot.ru/'.$debtor->getAttribute('href'));

        // $debtor_html = str_get_html((string) $debtor_page->getBody());
        $reestr_card = $debtor_html->find('.reestr_card', 0);

        $debtor_data = [
            'debtor_id' => end($debtor_id),
            'name' => $reestr_card->find('tr', 0)->find('td', 1)->innertext,
            'region' => $reestr_card->find('tr', 2)->find('td', 1)->innertext,
            'inn' => $reestr_card->find('tr', 4)->find('td', 0)->innertext == 'ИНН' ? $reestr_card->find('tr', 4)->find('td', 1)->innertext : $reestr_card->find('tr', 3)->find('td', 1)->innertext,
            'url' => $debtor->getAttribute('href')
        ];

        $debtor_nalog = file_get_contents('https://bo.nalog.ru/nbo/organizations/search?query='.$debtor_data['inn']);
        $debtor_nalog = json_decode($debtor_nalog, true);
        if(!empty($debtor_nalog['content'][0])) {
            $debtor_data['bo_nalog'] = json_encode($debtor_nalog['content'][0]);
            $debtor_data['status'] = !empty($debtor_nalog['content'][0]['statusCode']) ? $debtor_nalog['content'][0]['statusCode'] : '';
        }

        $debtor_id = $db->insert('debtors', $debtor_data);

        $debtor_html->clear();
        unset($reestr_card);
        unset($debtor_page);
        unset($debtor_html);
    }

}

print_r("Лоты добавленны");

function searchInnsInText($text) {
    $allStatuses = NULL;
    if(!empty($text)) {
        preg_match_all('#(?<!\d)\d{10}(?!\d)#', $text, $find_inn);
        foreach(array_unique($find_inn[0]) as $inn) {
            $get_status = getCompBfoStatus($inn);
            if($get_status) {
                $allStatuses[] = $get_status;
            }
        }
        $allStatuses = json_encode($allStatuses);
    }
    return $allStatuses;
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