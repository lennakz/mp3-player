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
	
	public function __construct($filename = null)
	{
		if (!empty($filename))
			$this->filename = trim($filename);
		else
			$this->filename = '';
	}
	
	public static function load()
	{
		$audios = [];
		
		$lines = file(self::AUDIODATA);

		foreach ($lines as $filename)
		{
						
			$audio = new Audio($filename);
			
			$audios[] = $audio;
		}

		return $audios;
	}
	
	public function saveName()
	{
		$line = $this->filename;
		$fp = fopen(self::AUDIODATA, 'a+');
		fwrite($fp, $line);
		fclose($fp);
	}
	
	public function getInfo()
	{
		$getID3 = new getID3;
		$file_info_total = $getID3->analyze($this->getFullPath());
		getid3_lib::CopyTagsToComments($file_info_total);
		$file_info = empty($file_info_total['id3v1']) ? [] : $file_info_total['id3v1'];
		
		return $file_info;
	}
	
	public function getTitle()
	{
		return !isset($this->getInfo()['title']) ? 'Unknown Title' : $this->getInfo()['title'];
	}
	
	public function getArtist()
	{
		return !isset($this->getInfo()['artist']) ? 'Unknown Artist' : $this->getInfo()['artist'];
	}
	
	public function getAlbum()
	{
		return !isset($this->getInfo()['album']) ? 'Unknown Album' : $this->getInfo()['album'];
	}
	
	public function getYear()
	{
		return !isset($this->getInfo()['year']) ? 'Unknown Year' : $this->getInfo()['year'];
	}
	
	public function getGenre()
	{
		return !isset($this->getInfo()['genre']) ? 'Other' : $this->getInfo()['genre'];
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
	
	public function getFilename()
	{
		return $this->filename;
	}
	
	public function setFilename($filename)
	{
		$this->filename = $filename;
		
		return $this;
	}
}
