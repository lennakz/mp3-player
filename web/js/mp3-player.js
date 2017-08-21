$(function () {
	var audio;

	// Base Audio Function
	function initAudio(element) {
		var song = element.data('song');
		var	cover = element.data('cover');
		var album = element.data('album');
		var	artist = element.data('artist');
		var title = element.text();

		audio = new Audio(song);

		$('#title').text(title);
		$('#album').text(album);
		$('#artist').text(artist);

		$('#cover-img').attr('style', 'background-image: url(images/covers/' + cover + ')');

		$('#playlist li').removeClass('active');
		element.parent().addClass('active');
	}
	
	function showAudioTime() {
		var duration = parseInt(audio.duration);
		$(audio).bind('timeupdate', function () {

			// Show duration time
			var timeline = $('#duration');
			var s_current = parseInt(audio.currentTime % 60);
			var	m_current = parseInt(audio.currentTime / 60) % 60;
			var value;

			if (s_current < 10) {
				timeline.html(m_current + ':0' + s_current);
			} else {
				timeline.html(m_current + ':' + s_current);
			}

			if (audio.currentTime > 0) {
				value = Math.floor((100 / audio.duration) * audio.currentTime);
			}
			
			$('#progress').css('width', value + '%');

			// Show time left
			var currentTime = parseInt(audio.currentTime);
			var timeLeft = duration - currentTime;
			var s_left = timeLeft % 60;
			var m_left = Math.floor(timeLeft / 60) % 60;
			var h_left = Math.floor(timeLeft / 360) % 60;

			s_left = s_left < 10 ? "0" + s_left : s_left;
			m_left = m_left < 10 ? "0" + m_left : m_left;
			h_left = h_left < 10 ? "0" + h_left : h_left;

			$('#timeleft').html(h_left + ':' + m_left + ':' + s_left);

			if (timeLeft === 0) {
				audio.next().play();
			}
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
		showAudioTime();
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
		
		audio.addEventListener('loadeddata', function () {
			showAudioTime();
		});
		
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
		
		audio.addEventListener('loadeddata', function () {
			showAudioTime();
		});
		
		audio.play();
	});

	// Click a Song in Playlist
	$('body').on('click', '#playlist li', function (event) {
		audio.pause();
		initAudio($(this).find('span'));
		$('#play-btn').hide();
		$('#pause-btn').show();
		
		audio.addEventListener('loadeddata', function () {
			showAudioTime();
		});
		
		audio.play();
	});

	// Volume Control
	$('#volume-slider').change(function () {
		audio.volume = parseFloat(this.value / 10);
	});

	// Seek Control
	audio.addEventListener('loadeddata', function () {
		$('#progress-bar').click(function (event) {
			var total = $(this).width();
			var	current = event.offsetX;
			var	ratio = current / total;
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
