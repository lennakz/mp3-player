<?php

$base_url = $_SERVER['DOCUMENT_ROOT'] . '/mp3-player';
require $base_url . '/source/getid3/getid3.php';

function load_music_list()
{
	global $base_url;
	$list = '';
	
	if (empty(glob($base_url . '/music/*')))
	{
		$list = 'No audio files found';
	}
	else
	{
		foreach (glob($base_url . '/music/*') as $k => $file) 
		{
			$getID3 = new getID3;

			$file_info_total = $getID3->analyze($file);
			getid3_lib::CopyTagsToComments($file_info_total);
			$file_info = $file_info_total['comments_html'];
			
			$cover_filename = str_replace(['.mp3', $base_url . '/music/'], ['.jpg', ''], $file);
			$cover = file_exists($base_url . '/images/covers/' . $cover_filename) ? $cover_filename : 'default.png';

			$filename = str_replace($base_url . '/', '', $file);
			$album = empty($file_info['album'][0]) ? 'Unknown Album' : $file_info['album'][0];
			$artist = empty($file_info['artist'][0]) ? 'Unknown Artist' : $file_info['artist'][0];
			$title = empty($file_info['title'][0]) ? ucfirst(str_replace([$base_url . '/music/', '.mp3'], ['', ''], $file)) : $file_info['title'][0];
			$class = $k === 0 ? ' class="active"' : '';

			$list .= 
				'<li' . $class . '>' .
					'<span data-song="' . $filename . '"' .
						' data-cover="' . $cover . '"' .
						' data-album="' . $album . '"' .
						' data-fullpath="' . $file . '"' .
						' data-artist="' . $artist . '">' .
						$title .
					'</span>' .
					'<button id="delete">X</button>' .
				'</li>';
		}
	}
	
	return $list;
}