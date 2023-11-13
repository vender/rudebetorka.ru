<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
require '../vendor/autoload.php';

session_start();
require_once '../config.php';
require_once BASE_PATH_ADMIN . '/includes/auth_validate.php';

date_default_timezone_set('Etc/UTC');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sendType = $_POST['send-type'];
    $template = !empty($_POST['template']) ? $_POST['template'] : false;
    $senderId = $_POST['senderId'];
    $regionId = !empty($_POST['region-id']) ? $_POST['region-id'] : false;
    $userList = !empty($_POST['user-list']) ? $_POST['user-list'] : false;
    $sendTryId = time();

    $db = getDbInstance();

    if($template) {
        $db->where('id', $_POST['template']);
        $template = $db->getOne("sender_templates");
        $atachments = $template['atachments'] ? json_decode($template['atachments']) : false;
    }

    if($sendType == 1) {
        $select = array('c.id', 'c.name', 'c.socilas', 'c.phone', 'c.url', 'c.fio', 'c.email', 'c.region', 'c.created_at', 'c.updated_at', 'c.status', 'r.name as regionName', 'r.id as regionId', 's.name as statusname', 's.color');
        $db->join("regions r", "c.region=r.id", "LEFT");
        $db->join("statuses s", "c.status=s.id", "LEFT");

        $db->orderBy('regionName', 'ASC');
        $customers = $db->get('customers c', null, $select);

        $select_detstvo = array('cd.id', 'cd.name', 'cd.region', 'cd.status', 'cd.email', 'r.name as regionName', 'r.id as regionId');
        $db->join("regions r", "cd.region=r.id", "LEFT");

        $db->orderBy('regionName', 'ASC');
        $customers_detstvo = $db->get('customers_detstvo cd', null, $select_detstvo);

        $allCustomers = array_merge($customers, $customers_detstvo);
        
        $user_by_region = array_filter($allCustomers, function($customer) use ($regionId) {
            return $customer['regionId'] == $regionId;
        });

        $mailist = removeDoubles($user_by_region);

        // print_r($mailist);
        // file_put_contents('log.txt', date('Y-m-d G:i:s').' Отчет: ' . print_r($mailist, 1)."\n", FILE_APPEND);
    } else if($sendType == 2 && !empty($userList)) {
        $db->where ("id", $userList, 'in');
        $customers = $db->get("customers");

        $db->where ("id", $userList, 'in');
        $customers_detstvo = $db->get("customers_detstvo");

        $allCustomers = array_merge($customers, $customers_detstvo);

        $mailist = removeDoubles($allCustomers);

        // print_r($mailist);
        // file_put_contents('log.txt', date('Y-m-d G:i:s').' Отчет: ' . print_r($mailist, 1)."\n", FILE_APPEND);
    } else {
        $customers = $db->get("customers", null, ['id', 'name', 'email']);
        $customers_detstvo = $db->get("customers_detstvo", null, ['id', 'name', 'email']);
        $allCustomers = array_merge($customers, $customers_detstvo);
        $mailist = removeDoubles($allCustomers);
        
        // $mailist = [
        //     ['email' => 'venderu@gmail.com', 'name' => 'Vender Bender'],
        //     ['email' => 'venderu@yandex.ru', 'name' => 'Vender Yender'],
        // ];
    }


    $mail = new PHPMailer();
    // $mail->isSMTP();
    $mail->CharSet = "utf-8";
    // $mail->isSendmail();
    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    // $mail->Host = 'mail.kinohitruk.ru';
    // $mail->Port = 25;
    // $mail->SMTPAuth = true;
    // $mail->Username = 'info@kinohitruk.ru';
    // $mail->Password = 'HhgBnmVccG6791#sfV%!';
    $mail->setFrom('info@kinohitruk.ru', 'Академия Хитрука');
    $mail->addReplyTo('info@kinohitruk.ru', 'Академия Хитрука');
    $mail->Subject = $template['name'];
    
    foreach ($mailist as $row) {
        $replace = !empty($row['email']) ? ['{$email}' => $row['email']] : '';

        $mail->msgHTML(strtr($template['text'], $replace));
        // file_put_contents('log.txt', date('Y-m-d G:i:s').' Отчет: ' . print_r($row, 1)."\n", FILE_APPEND);

        try {
            $mail->addAddress($row['email'], $row['name']);
        } catch (Exception $e) {
            echo 'Invalid address skipped: ' . htmlspecialchars($row['email']) . '<br>';
            file_put_contents('error.log', date('Y-m-d G:i:s').' Ошибка: ' . print_r('Invalid address skipped: ' . htmlspecialchars($row['email']), 1)."\n", FILE_APPEND);
            continue;
        }
        if($atachments) {
            foreach ($atachments as $atachment) {
                $mail->addAttachment($atachment);
            }
        }
    
        try {
            $mail->send();
            echo 'Отправлено: ' . htmlspecialchars($row['name']) . ' (' . htmlspecialchars($row['email']) . ')<br>';
            $data_to_log['name'] = $row['name'];
            $data_to_log['email'] = $row['email'];
            $data_to_log['region-id'] = $regionId;
            $data_to_log['send-type'] = $sendType;
            $data_to_log['template'] = $template['id'];
            $data_to_log['sendTryId'] = $sendTryId;
            $data_to_log['senderId'] = $senderId;
            $last_id = $db->insert('sender_log', $data_to_log);
            
            file_put_contents('error.log', date('Y-m-d G:i:s').': ' . print_r($db->getLastError(), 1)."\n", FILE_APPEND);
            // file_put_contents('log.txt', date('Y-m-d G:i:s').': ' . print_r($db->getLastError(), 1)."\n", FILE_APPEND);
            //Mark it as sent in the DB
        } catch (Exception $e) {
            echo 'Mailer Error (' . htmlspecialchars($row['email']) . ') ' . $mail->ErrorInfo . '<br>';
            file_put_contents('error.log', date('Y-m-d G:i:s').' Ошибка: ' . print_r('Mailer Error (' . htmlspecialchars($row['email']) . ') ' . $mail->ErrorInfo, 1)."\n", FILE_APPEND);
            //Reset the connection to abort sending this message
            //The loop will continue trying to send to the rest of the list
            // $mail->getSMTPInstance()->reset();
        }
        //Clear all addresses and attachments for the next iteration
        $mail->clearAddresses();
        $mail->clearAttachments();
    }

}

function removeDoubles($customers) {
    $arrToStrings = array_map("serialize", $customers);
    $accum = [];
    foreach($customers as $ck => $customer) {
        $searchEmail = !empty($customer['email']) ? trim($customer['email']) : false;
        if ($searchEmail) {
            $accum[$ck] = $searchEmail;
        }
    }

    $unic_emails = array_unique($accum, SORT_REGULAR);

    foreach($unic_emails as $ek => $unic_email) {
        $results[$ek] = $customers[$ek];
    }

    return $results;
}