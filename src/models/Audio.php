<?php

/**
 * Description of Audio
 *
 * @author nikolai
 */
class Audio
{
	public $upload;
	
	protected $name;
	protected $title;
	protected $artist;
	protected $album;
	protected $year;
	protected $genre;
	protected $cover_url;


	const AUDIODATA = __DIR__ . '/../../data/data.txt';
	
	public static function load()
	{
		$audios = [];
		$lines = file(self::AUDIODATA);

		foreach ($lines as $line)
		{
			$data = json_decode($line);
			
			if (!empty(glob(BASE_DIR . '/data/music/'.$data->name.'.*')))
			{
				$audio = new Audio();
				$audio->setAttributes($data);
				
				if (glob(BASE_DIR . '/data/images/covers/' . $data->name . '{.jpg, .png}', GLOB_BRACE))
					$audio->setCoverUrl($data->name);
								
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
		$audio_info = self::getInfo($this->upload->getPathname());
		$name = str_replace('.'.$this->upload->getClientOriginalExtension(), '', $this->upload->getClientOriginalName());

		$data = [
			'name' => $name,
		];

		$defaults = [
			'title' => $this->beautifyTitle($name),
			'artist' => 'Unknown Artist',
			'album' => 'Unknown Album',
			'year' => 'Unknown Year',
			'genre' => 'Other',
			'cover_url' =>  BASE_URL . '/data/images/covers/default.png',
		];
		
		foreach ($defaults as $k => $default)
			$data[$k] = !empty($audio_info[$k]) ? $audio_info[$k] : $default;
				
		file_put_contents(self::AUDIODATA, json_encode($data) . PHP_EOL, FILE_APPEND);
		
		$file_dir = dirname(__DIR__, 2) . '/data/music/';
		
		$this->upload->move($file_dir, $this->upload->getClientOriginalName());
	}
	
	protected function beautifyTitle($title)
	{
		$beatiful_title = preg_replace(['/\-|\_|\./', '/\S\(/', '/\)\S/'], [' ', ' (', ') '], $title);
		$beatiful_title = ucwords($beatiful_title);
		
		return $beatiful_title;
	}
	
	public static function getInfo($path)
	{
		$getID3 = new getID3;
		$file_info_total = $getID3->analyze($path);
		getid3_lib::CopyTagsToComments($file_info_total);
		$file_info = empty($file_info_total['id3v1']) ? [] : $file_info_total['id3v1'];
		
		return $file_info;
	}
	
	public function getName()
	{
		return $this->name;
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
	
	public function getCoverUrl()
	{
		return $this->cover_url;
	}
	
	public function setCoverUrl($name)
	{
		$this->cover_url = BASE_URL . '/data/images/covers/' . $name . '.jpg';
	}
	
	public function getFullPath()
	{
		return BASE_DIR . '/data/music/' . $this->name . '.mp3';
	}

	public function getAudioUrl()
	{
		return BASE_URL . '/data/music/' . $this->name . '.mp3';
	}
		
}
