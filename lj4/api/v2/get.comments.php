<?php

declare(strict_types=1);


header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Content-Type: text/json');

include_once __DIR__ . '/Comments.class.php';
include_once __DIR__ . '/parse.xml.php';

if (isset($_GET['rss'])) {
    $link = $_GET['rss'];
    if (!empty($link) && preg_match("#^https://einsteinpower.edublogs.org#", $link)) {

        $comments = new Comments($link);

        $out = $comments->getComments();

        echo json_encode($out);
    }
}
