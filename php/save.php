<?php

$base_url = $_SERVER['DOCUMENT_ROOT'];
require $base_url . '/php/functions.php';

$save_dir = $base_url . '/music/';
$list = '';

function reArrayFiles($data) {

    $arr = [];
    $file_count = count($data['name']);
    $file_keys = array_keys($data);

    for ($i=0; $i < $file_count; $i++)
	{
        foreach ($file_keys as $key)
            $arr[$i][$key] = $data[$key][$i];
    }

    return $arr;
}

if (empty($_FILES))
{
	echo 'Files are too large to load at once. Please load by smaller portions.';
	exit;
}
else
{
	foreach (reArrayFiles($_FILES['file']) as $file)
	{
		if (strpos($file['type'], 'audio/') === false)
		{
			echo $file['type'] . 'One of the files is not audio file';
			exit;
		}
		else
		{
			move_uploaded_file($file['tmp_name'], $save_dir . $file['name']);		
		}
	}
}

echo load_music_list();