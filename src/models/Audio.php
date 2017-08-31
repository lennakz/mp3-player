<?php

/**
 * Description of Audio
 *
 * @author nikolai
 */
class Audio
{
	public $upload;
	
	protected $filename;
	protected $title;
	protected $artist;
	protected $album;
	protected $year;
	protected $genre;
	
	const AUDIODATA = __DIR__ . '/../../data/data.txt';
	
//	public function __construct($filename = null)
//	{
//		if (!empty($filename))
//			$this->filename = trim($filename);
//		else
//			$this->filename = '';
//	}
	
	public static function load()
	{
		$audios = [];
		$lines = file(self::AUDIODATA);

		foreach ($lines as $line)
		{
			$data = json_decode($line);
			
			if (!empty(glob(dirname(__DIR__, 2).'/data/music/'.$data->filename.'.*')))
			{
				$audio = new Audio();
				$audio->setAttributes($data);
				
				$audios[] = $audio;
			}
		}	
		
		return $audios;
	}
	
	protected function setAttributes($data)
	{
		foreach ($this as $attribute => $value)
		{
			if (isset($data->$attribute))
				$this->$attribute = $data->$attribute;
		}
	}
	
	public function save()
	{
		$audio_info= self::getInfo($this->upload->getPathname());
		$filename = str_replace('.'.$this->upload->getClientOriginalExtension(), '', $this->upload->getClientOriginalName());
		$data = [
			'filename' => $filename,
			'title' => (!isset($audio_info['title']) or empty($audio_info['title'])) ? $this->beautifyTitle($filename) : $audio_info['title'],
			'artist' => (!isset($audio_info['artist']) or empty($audio_info['artist'])) ? 'Unknown Artist' : $audio_info['artist'],
			'album' => (!isset($audio_info['album']) or empty($audio_info['album'])) ? 'Unknown Album' : $audio_info['album'],
			'year' => (!isset($audio_info['year']) or empty($audio_info['year'])) ? 'Unknown Year' : $audio_info['year'],
			'genre' => (!isset($audio_info['genre']) or empty($audio_info['genre'])) ? 'Other' : $audio_info['genre'],
		];
		
		$fp = fopen(self::AUDIODATA, 'a+');
		fwrite($fp, json_encode($data).PHP_EOL);
		fclose($fp);
		
		$file_dir = dirname(__DIR__, 2) . '/data/music/';
		
		$this->upload->move($file_dir, $this->upload->getClientOriginalName());
	}
	
	protected function beautifyTitle($title)
	{
		return '';
	}
	
	public static function getInfo($path)
	{
		$getID3 = new getID3;
		$file_info_total = $getID3->analyze($path);
		getid3_lib::CopyTagsToComments($file_info_total);
		$file_info = empty($file_info_total['id3v1']) ? [] : $file_info_total['id3v1'];
		
		return $file_info;
	}
	
	public function getFilename()
	{
		return $this->filename;
	}
	
	public function getTitle()
	{
		return $this->title;
	}
	
	public function getArtist()
	{
		return $this->artist;
	}
	
	public function getAlbum()
	{
		return $this->album;
	}
	
	public function getYear()
	{
		return $this->year;
	}
	
	public function getGenre()
	{
		return $this->genre;
	}
	
	public function getFullPath()
	{
		return dirname(__DIR__, 2) . '/data/music/' . $this->filename . '.mp3';
	}

	public function getAudioUrl()
	{
		return '/mp3-player/data/music/' . $this->filename . '.mp3';
	}
	
	public function getCoverUrl()
	{
		return '/mp3-player/data/images/covers/' . $this->filename . '.jpg';
	}

}
