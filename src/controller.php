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
	$audios = Audio::loadAll();
	
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
		
		$audios = Audio::loadAll();
		
		return new Response($app['twig']->render('partials/audio_list.twig', ['audios' => $audios]));
	}
	
	return $app['twig']->render('index.twig', [
		'form' => $form->createView(),
		'audios' => $audios,
	]);
});

$app->match('/delete', function (Request $request) use ($app)
{
	$id = $request->request->get('id');
	Audio::delete($id);
		
	$audios = Audio::loadAll();
	
	return new Response($app['twig']->render('partials/audio_list.twig', ['audios' => $audios]));	
});
