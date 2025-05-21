<?php

header('Access-Control-Allow-Origin: https://lejow.github.io');

if (isset($_POST['lang']) && isset($_FILES['file-to-process'])) {
    $lang = $_POST['lang'];
    $file = $_FILES['file-to-process'];
    if (isset($file['error']) && $file['error'] == 0) {
        $file_path = $file['tmp_name'];
        $pwd = __DIR__;
        $output;
        $return;
        switch ($lang) {
            case 'fr-FR':
                $dic = 'hyph_fr.dic';
                break;
            case '_fr-FR':
                $dic = 'hyph_fr.dic.1';
                break;
            case 'la-VA':
                $dic = 'hyph_la_VA.dic';
                break;
            case 'en-US':
                $dic = 'hyph_en_US.dic';
                break;
        }
        echo `$pwd/bin/hyph/example $pwd/bin/hyph/$dic $file_path`;
    }
}
