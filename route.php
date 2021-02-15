<?php
try {

	$uri_parse = parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
    $init_path = '/cotacao/';
	
    switch ($uri_parse) {
        case $init_path:
            include('view/quote.php');
            break;
        case $init_path. 'api/consultar/':
            include('php/consult.php');
            break;
        case $init_path. 'api/adicionar/':
            include('php/insert.php');
        break;
        case $init_path. 'api/salvar/':
            include('php/save.php');
        break;
        default:
            include('view/404.php');
            break;
    }
} catch (Exception $e) {
    echo 'Error:' . $e;
}
    