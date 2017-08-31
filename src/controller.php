<?php

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

require 'models/Audio.php';

$app->match('/', function (Request $request) use ($app)
{
	$p['audios'] = Audio::load();
	
	$p['form'] = $app['form.factory']->createBuilder(FormType::class)
		->add('upload', FileType::class, ['label' => 'Upload your music', 'multiple' => true])
		->add('submit', SubmitType::class, [
			'label' => 'Upload',
		])
		->getForm();

	$p['form']->handleRequest($request);
	
	if ($p['form']->isSubmitted() and $p['form']->isValid())
	{
		foreach ($p['form']->getData()['upload'] as $a)
		{
			$audio = new Audio();
			$audio->upload = $a;
			$audio->save();
		}
		return $app->redirect('/mp3-player/web/index.php');
	}

	$p['form'] = $p['form']->createView();
	
	return $app['twig']->render('index.twig', $p);
});
