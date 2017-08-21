<?php

$base_url = $_SERVER['DOCUMENT_ROOT'] . '/mp3-player';
require $base_url . '/php/functions.php';

unlink($_POST['filename']);

echo load_music_list();
