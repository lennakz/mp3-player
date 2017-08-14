<?php

$base_url = $_SERVER['DOCUMENT_ROOT'];
require $base_url . '/php/functions.php';

unlink($_POST['filename']);

echo load_music_list();
