$(function () {
	var audio;

	// Base Audio Function
	function initAudio(element) {
		var song = element.data('song'),
				cover = element.data('cover'),
				album = element.data('album'),
				artist = element.data('artist'),
				title = element.text();

		audio = new Audio(song);

		$('#title').text(title);
		$('#album').text(album);
		$('#artist').text(artist);

		$('#cover-img').attr('style', 'background-image: url(images/covers/' + cover + ')');

		$('#playlist li').removeClass('active');
		element.parent().addClass('active');
	}

	// Time - Show Duration
	function showDuration() {
		$(audio).bind('timeupdate', function () {
			var timeline = $('#duration'),
					s = parseInt(audio.currentTime % 60),
					m = parseInt(audio.currentTime / 60) % 60,
					value;

			if (s < 10) {
				timeline.html(m + ':0' + s);
			} else {
				timeline.html(m + ':' + s);
			}

			if (audio.currentTime > 0) {
				value = Math.floor((100 / audio.duration) * audio.currentTime);
			}
			$('#progress').css('width', value + '%');
		});
	}

	// Time - Show Time Left
	function showTimeLeft() {
		var duration = parseInt(audio.duration);

		$(audio).bind('timeupdate', function () {
			var currentTime = parseInt(audio.currentTime),
					timeLeft = duration - currentTime,
					s, m, h;

			s = timeLeft % 60;
			m = Math.floor(timeLeft / 60) % 60;
			h = Math.floor(timeLeft / 360) % 60;

			s = s < 10 ? "0" + s : s;
			m = m < 10 ? "0" + m : m;
			h = h < 10 ? "0" + h : h;

			$('#timeleft').html(h + ':' + m + ':' + s);
			
			if (timeLeft === 0) {
				audio.next().play();
			}
		});
	}

	// Wait to Finish Loading Audio Files
	function waitAndShowTime() {
		audio.addEventListener('loadeddata', function () {
			showDuration();
			showTimeLeft();
		});
	}

	// Hide Pause Button
	$('#pause-btn').hide();

	// Set the Initial Audio
	initAudio($('#playlist li:first-child span'));

	// Play Button
	$('body').on('click', '#play-btn', function (event) {
		event.preventDefault();
		$('#play-btn').hide();
		$('#pause-btn').show();
		showDuration();
		showTimeLeft();
		audio.play();
	});

	// Pause Button
	$('body').on('click', '#pause-btn', function (event) {
		event.preventDefault();
		audio.pause();
		$('#play-btn').show();
		$('#pause-btn').hide();
	});

	// Stop Button
	$('body').on('click', '#stop-btn', function (event) {
		event.preventDefault();
		audio.pause();
		$('#play-btn').show();
		$('#pause-btn').hide();
		audio.currentTime = 0;
		$('#progress').css('width', '0%');
	});

	// Next Button
	$('body').on('click', '#next-btn', function (event) {
		event.preventDefault();
		audio.pause();
		$('#play-btn').hide();
		$('#pause-btn').show();
		var next = $('#playlist li.active').next();
		if (next.length === 0) {
			next = $('#playlist li:first-child');
		}

		initAudio(next.children(':first'));
		waitAndShowTime();
		audio.play();
	});

	// Prev Button
	$('body').on('click', '#prev-btn', function (event) {
		event.preventDefault();
		audio.pause();
		$('#play-btn').hide();
		$('#pause-btn').show();
		var prev = $('#playlist li.active').prev();
		if (prev.length === 0) {
			prev = $('#playlist li:last-child');
		}
		initAudio(prev.children(':first'));
		waitAndShowTime();
		audio.play();
	});

	// Click a Song in Playlist
	$('body').on('click', '#playlist li', function (event) {
		audio.pause();
		initAudio($(this).find('span'));
		$('#play-btn').hide();
		$('#pause-btn').show();
		waitAndShowTime();
		audio.play();
	});

	// Volume Control
	$('#volume-slider').change(function () {
		audio.volume = parseFloat(this.value / 10);
	});

	// Seek Control
	audio.addEventListener('loadeddata', function () {
		$('#progress-bar').click(function (event) {
			var total = $(this).width(),
				current = event.offsetX,
				ratio = current / total;
			var duration = audio.duration * ratio;
			audio.currentTime = duration;
			$('#progress').css('width', Math.floor(ratio * 100) + '%');
		});
	});

	// Ajax call to upload file
	$('#form').submit(function () {
		var $form = $(this);
		var formdata = false;
		if (window.FormData) {
			formdata = new FormData($form[0]);
		}
		$.ajax({
			url: "php/save.php",
			type: "POST",
			data: formdata ? formdata : $form.serialize(),
			contentType: false,
			cache: false,
			processData: false
		}).done(function (response) {
			$('#playlist').html(response);
		}).fail(function (jqXHR, textStatus, errorThrown) {
			console.log(jqXHR);
			console.log(textStatus);
			console.log(errorThrown);
		});

		return false;
	});

	// Ajax call to delete file
	$('body').on('click', '#delete', function () {
		var filename = $(this).parent().find('span').data('fullpath');
		$.ajax({
			url: "php/delete.php",
			type: "POST",
			data: {filename: filename}
		}).done(function (response) {
			$('#playlist').html(response);
		});

		return false;
	});

});
