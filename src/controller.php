<?php

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

require 'models/Audio.php';

$app->get('/', function () use ($app)
{
	$p['audios'] = Audio::load();

	return $app['twig']->render('index.twig', $p);
});


$app->match('/form', function (Request $request) use ($app)
{
	$audio = new Audio();
	$form = $app['form.factory']->createBuilder(FormType::class, $audio)
		->add('upload', FileType::class, ['label' => 'Audiofile (mp3)'])
		->add('submit', SubmitType::class, [
			'label' => 'Upload',
		])
		->getForm();

	$form->handleRequest($request);
	
	if ($form->isSubmitted() and $form->isValid())
	{
		$name = $audio->upload->getClientOriginalName();
		$extension = $audio->upload->getClientOriginalExtension();
		$filename = basename($name, '.'.$extension);de($filename);
		$audio->setFilename($filename);		
		$audio->saveName();
		$audio->upload->move(__DIR__ . '/../../data/music/', $audio->upload->getClientOriginalName());
	}
	
	return $app['twig']->render('form.twig', array('form' => $form->createView()));
});
