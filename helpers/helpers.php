<?php
/**
 * Function to generate random string.
 */
function randomString($n) {

	$generated_string = "";

	$domain = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";

	$len = strlen($domain);

	// Loop to create random string
	for ($i = 0; $i < $n; $i++) {
		// Generate a random index to pick characters
		$index = rand(0, $len - 1);

		// Concatenating the character
		// in resultant string
		$generated_string = $generated_string . $domain[$index];
	}

	return $generated_string;
}

/**
 *
 */
function getSecureRandomToken() {
	$token = bin2hex(openssl_random_pseudo_bytes(16));
	return $token;
}

/**
 * Clear Auth Cookie
 */
function clearAuthCookie() {

	unset($_COOKIE['series_id']);
	unset($_COOKIE['remember_token']);
	setcookie('series_id', null, -1, '/');
	setcookie('remember_token', null, -1, '/');
}
/**
 *
 */
function clean_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

function paginationLinks($current_page, $total_pages, $base_url) {

	if ($total_pages <= 1) {
		return false;
	}

	$html = '';

	if (!empty($_GET)) {
		// We must unset $_GET[page] if previously built by http_build_query function
		unset($_GET['page']);
		// To keep the query sting parameters intact while navigating to next/prev page,
		$http_query = "?" . http_build_query($_GET);
	} else {
		$http_query = "?";
	}

	$html = '<nav><ul class="pagination justify-content-center">';

	if ($current_page == 1) {
		$html .= '<li class="page-item disabled"><a class="page-link"><span aria-hidden="true">&laquo;</span></a></li>';
	} else {
		$html .= '<li class="page-item"><a class="page-link" href="' . $base_url . $http_query . '&page=1"><span aria-hidden="true">&laquo;</span></a></li>';
	}

	// Show pagination links

	//var i = (Number(data.page) > 5 ? Number(data.page) - 4 : 1);

	if ($current_page > 5) {
		$i = $current_page - 4;
	} else {
		$i = 1;
	}

	for (; $i <= ($current_page + 4) && ($i <= $total_pages); $i++) {
		($current_page == $i) ? $li_class = ' class="page-item active" aria-current="page"' : $li_class = ' class="page-item"';

		$link = $base_url . $http_query;

		$html = $html . '<li' . $li_class . '><a class="page-link" href="' . $link . '&page=' . $i . '">' . $i . '</a></li>';

		if ($i == $current_page + 4 && $i < $total_pages) {

			$html = $html . '<li class="page-item disabled"><a class="page-link">...</a></li>';

		}

	}

	if ($current_page == $total_pages) {
		$html .= '<li class="page-item disabled"><a class="page-link"><span aria-hidden="true">&raquo;</span></a></li>';
	} else {

		$html .= '<li class="page-item"><a class="page-link" href="' . $base_url . $http_query . '&page=' . $total_pages . '"><span aria-hidden="true">&raquo;</span></a></li>';
	}

	$html = $html . '</ul></nav>';

	return $html;
}

/**
 * to prevent xss
 */
function xss_clean($string = ''){
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');

}

function search_inn($text) {
    preg_match_all('#(?<!\d)\d{10}(?!\d)#', $text, $find_inn);
	
    if(!empty($find_inn[0])) {

        foreach(array_unique($find_inn[0]) as $inn) {
            $nalog = file_get_contents('https://bo.nalog.ru/nbo/organizations/search?query='.$inn);
            $nalog = json_decode($nalog, true);
            $inn_status = '<span class="legend-indicator"></span>';
            if(!empty($nalog['content'][0]['statusCode'])) {
                switch ($nalog['content'][0]['statusCode']) {
                    case 'ACTIVE':
                        $inn_status = '<span class="legend-indicator bg-success"></span>';
                        break;
                    case 'LIQUIDATION_STAGE':
                        $inn_status = '<span class="legend-indicator bg-warning"></span>';
                        break;
                    case 'INACTIVE':
                        $inn_status = '<span class="legend-indicator bg-danger"></span>';
                        break;
                }
            }

            $text = str_replace($inn, '<mark id="'.$inn.'" style="cursor: pointer;" data-bs-toggle="modal" data-bs-inn="'.$inn.'" data-bs-target="#editUserModal"><span data-bs-toggle="tooltip" data-bs-html="true" title="'.getBfoNalog($nalog).'">'.$inn_status.$inn.'</span></mark>', $text, $count);
        }
        
    };

    return $text;
}

function getBfoNalog($nalog) {
    if(!empty($nalog)) {
        $output = '';
        if(!empty($nalog['content'][0])){
            foreach($nalog['content'][0]['bfo'] as $year) {
                $output .= $year['period'].'г. - <span>'.number_format($year['actives'] * 1000, 0, ',', ' ').' ₽</span><br>';
            }
        } else {
            $output = 'с 2019г. по 2022г. <br>данных нет';
        }
        
        return $output;
    }
}