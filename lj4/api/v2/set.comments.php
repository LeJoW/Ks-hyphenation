<?php

declare(strict_types=1);

include_once __DIR__ . '/Comments.class.php';
include __DIR__ . '/parse.xml.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');

if ($input = file_get_contents('php://input')) {
    $input = json_decode($input, true);
    if ($input && isset($input["comment"])) {
        $new_comment = $input['comment'];
        if (isset($new_comment["ref"])) {
            $ref = $new_comment["ref"];

            $comments = new Comments($new_comment["ref"]);

            echo json_encode([
                "success" => $comments->addPrivateComment($new_comment)
            ]);
        } else {
            echo json_encode([
                "success" => false
            ]);
        }
    }
}
