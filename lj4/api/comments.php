<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: text/xml');

if ($_GET['rss']) {
    $link = $_GET['rss'];
    if (!empty($link)) {
        $edublog_comments = `wget '$link' -O /dev/stdout`;
        echo $edublog_comments;
    }
}
