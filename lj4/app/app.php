<?php
//
//  app.php
//  ljw
//
//  Created by Léon-Joseph on 23/02/2018.
//  Copyright © 2018 ljw. All rights reserved.
//

#--INCLUDE
include_once '/volume1/Serveur/www/lib/composer/vendor/autoload.php';

#--VARIABLES
$loader = new Twig_Loader_Filesystem(__DIR__ . '/view/');
//$twig = new Twig_Environment($loader, array('cache' => __DIR__ . '/../cache/'));
$twig = new Twig_Environment($loader, []);

$roads = json_decode(file_get_contents(__DIR__ . '/roads.json'), true);
$URL = preg_replace('#(.*)\?.*#', '$1', $_SERVER['REQUEST_URI']);
$twig_vars = array(
    '_dir_' => __DIR__ . '/view'
);
#--TRAITEMENT
if ($URL == '' or $URL == '/') {
    $URL = '/';
} elseif (isset($roads[$URL])) { } else {
    $URL = '/error.404';
}
$request = $roads[$URL];

#--REPONSE
if (is_array($request)) {
    if (isset($request[1])) {
        $php_file = __DIR__ . '/php/' . $request[0];
        $twig_file = __DIR__ . '/view/' . $request[1];
        if (is_file($php_file) and is_file($twig_file)) {
            include $php_file;
            echo $twig->render($request[1], $twig_vars);
        }
    }
} elseif (is_file(__DIR__ . '/php/' . $request)) {
    include __DIR__ . '/php/' . $request;
} else {
    include __DIR__ . '/errors/error.500.php';
}
