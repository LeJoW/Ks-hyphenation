<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: text/json');

include_once __DIR__ . '/parse.xml.php';
include_once __DIR__ . '/Comments.class.php';

$posts = XMLtoArray(`wget 'https://einsteinpower.edublogs.org/feed' -O /dev/stdout`);

$posts = array_map(function ($post) {
    return [
        "title" => $post["title"],
        "link" =>  $post["link"],
        "author" => $post["dc:creator"],
        "date" => $post["pubDate"],
        "category" => $post["category"],
        "content" => $post["content:encoded"],
        "comments_link" => $post["wfw:commentRss"],
        "comments_count" => (int)$post["slash:comments"] + (new Comments($post["wfw:commentRss"]))->getPrivateCommentsCount()
    ];
}, $posts["rss"]["channel"]["item"]);

echo json_encode($posts);
