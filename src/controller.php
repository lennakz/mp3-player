<?php

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

require 'models/Audio.php';

$app->match('/', function (Request $request) use ($app)
{
	$audios = Audio::load();
	
	$form = $app['form.factory']->createBuilder(FormType::class, null, ['attr' => ['id' => 'form']])
		->add('upload', FileType::class, ['label' => 'Upload your music', 'multiple' => true])
		->add('submit', SubmitType::class, [
			'label' => 'Upload',
		])
		->getForm();

	$form->handleRequest($request);
	
	if ($form->isSubmitted() and $form->isValid())
	{
		foreach ($form->getData()['upload'] as $a)
		{
			$audio = new Audio();
			$audio->upload = $a;
			$audio->save();
		}
		return $app->redirect('/mp3-player/web/index.php');
	}
	
	return $app['twig']->render('index.twig', [
		'form' => $form->createView(),
		'audios' => $audios,
	]);
});

$app->match('/ajax-upload', function()
{
	d($_FILES);
	return '';
});
