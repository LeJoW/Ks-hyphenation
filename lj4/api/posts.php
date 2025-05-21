<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: text/xml');

echo `wget 'https://einsteinpower.edublogs.org/feed' -O /dev/stdout`;
