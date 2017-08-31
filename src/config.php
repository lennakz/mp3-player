<?php

ini_set('upload_max_filesize', '20M');
ini_set('post_max_size', '20M');

// enable the debug mode
$app['debug'] = true;

// configure your app for the production environment
$app['twig.path'] = [__DIR__ . '/../view'];
