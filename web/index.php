<?php

ini_set('display_errors', 1);

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\AssetServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\LocaleServiceProvider;

require __DIR__ . '/../globals.php';
require_once __DIR__.'/../vendor/autoload.php';

$app = new Application();
$app->register(new TwigServiceProvider());
$app->register(new AssetServiceProvider());
$app->register(new LocaleServiceProvider());
$app->register(new TranslationServiceProvider(), array(
    'translator.domains' => array(),
));
$app->register(new FormServiceProvider());

require __DIR__.'/../src/config.php';
require __DIR__.'/../src/controller.php';

$app->run();