<?php
// Function to sanitize filename
function sanitizeFilename($filename) {
	// Remove any directory components
	$filename = basename($filename);
	
	// Remove .json extension if present
	$filename = preg_replace('/\.json$/', '', $filename);
	
	// Remove any characters that aren't alphanumeric, dash, or underscore
	$filename = preg_replace('/[^a-zA-Z0-9\-_]/', '', $filename);
	
	return $filename;
}

// Get the playlist name from the query string
$playlist_name = isset($_GET['playlist']) ? sanitizeFilename($_GET['playlist']) : '';

// Construct the full filename
$playlist_file = $playlist_name . '.json';

// Read the playlist file
$playlist = [];
if (!empty($playlist_file) && file_exists($playlist_file)) {
	$json_content = file_get_contents($playlist_file);
	$playlist = json_decode($json_content, true) ?: [];
}

// Get list of JSON files in the current directory
$json_files = glob("*.json");

// Generate HTML
echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
<head>

 <script>
function loadJSON(url, callback) {
	var xhr = new XMLHttpRequest();
	xhr.overrideMimeType("application/json");
	xhr.open('GET', url + '?_=' + new Date().getTime(), true);
	xhr.onreadystatechange = function () {
		if (xhr.readyState == 4 && xhr.status == "200") {
			callback(xhr.responseText);
		}
	};
	xhr.send(null);
}

// Usage:
document.addEventListener('DOMContentLoaded', function() {
	loadJSON('your_json_file.json', function(response) {
		// Parse JSON string into object
		var actual_JSON = JSON.parse(response);
		// Use your JSON data here
		console.log(actual_JSON);
	});
});
</script>

	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="robots" content="noindex, nofollow">
	<title>Playlist Name: {$playlist_name}</title>
	<style>
		body { 
			max-width: 800px;
			margin: 0 auto;
			padding: 20px; 
			font-family: "Nunito", sans-serif;
			font-optical-sizing: auto;
			font-weight: 300;
			font-style: normal;
			color: #fff;
			background-color:#000;
			}
			a{ color:#fff; }
		.header-title{
			display:flex;
			gap:10px;
			justify-content: center;
			align-items: center;
			font-size: 18px;
			margin: 0 0 20px 0;
		}
		h1 { color: #fff; text-transform: uppercase; font-weight:300;}
		.playlist-controls{display:flex; gap: 10px;}
		#controls{
			display:flex;
		    flex-direction: column;
			justify-content: center;
			align-items: center;
			gap: 5px;
		}
		#playlist { list-style-type: none; padding: 0; }
		#playlist li { 
			cursor: pointer; 
			padding: 10px; 
			background-color: #555; 
			margin-bottom: 5px; 
			transition: all 0.3s ease;
			border-radius: 6px;
		    color: white;
		}
		#playlist li:hover { background-color: #666; }
		#playlist li.current-track { 
			background-color: #333; 
			color: white; 
			font-weight: bold;
			box-shadow: 0 2px 5px rgba(0,0,0,0.2);
		}
		#audio-player { width: 100%; margin-bottom: 20px; }
		#controls { margin-bottom: 20px; }
		.switch {
			position: relative;
			display: inline-block;
			width: 60px;
			height: 34px;
		}
		.switch input {
			opacity: 0;
			width: 0;
			height: 0;
		}
		.autoplay-toggle{
			font-size:14px;
		}
		.slider {
			position: absolute;
			cursor: pointer;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			background-color: #ccc;
			transition: .4s;
			border-radius: 34px;
		}
		.slider:before {
			position: absolute;
			content: "";
			height: 26px;
			width: 26px;
			left: 4px;
			bottom: 4px;
			background-color: white;
			transition: .4s;
			border-radius: 50%;
		}
		input:checked + .slider {
			background-color: #2196F3;
		}
		input:checked + .slider:before {
			transform: translateX(26px);
		}
		.top-select{
			display:flex;
		    width: 100%;
			flex-direction:row;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 20px;
		}
		#playlist-select {
			padding: 10px;
			font-size: 16px;
			border-radius: 5px;
			border: 1px solid #ccc;
			width: 50%;
			min-width: auto;
			}			
		
		.btn{
			background-color:#336699;
				text-decoration: none;
				display:flex;
				justify-content: center;
				align-items: center;
				height: 40px;
				padding: 0 10px 0 10px;
				border-radius: 6px;
		}
	</style>
</head>
<body>
<div class="top-select">
	<select id="playlist-select">
		<option value="">Select a playlist</option> 
		{$playlist_options}
HTML;

foreach ($json_files as $file) {
	$file_name = basename($file, '.json');
	$selected = ($file_name === $playlist_name) ? 'selected' : '';
	echo "<option value=\"$file_name\" $selected>$file_name</option>";
}

echo <<<HTML
	</select>
	<a class="btn" href='/radio/builder.php'>Back to Builder</a>
	</div>
	<div class="playlist-controls">
	<audio id="audio-player" controls>
		Your browser does not support the audio element.
	</audio>
	<div id="controls">
		<label class="switch">
			<input type="checkbox" id="autoplay-toggle" checked>
			<span class="slider"></span>
		</label>
		<label class='autoplay-toggle' for="autoplay-toggle">Autoplay</label>
	</div>
	</div>
	<ul id="playlist">
HTML;

foreach ($playlist as $track) {
	echo "<li data-src='" . htmlspecialchars($track['url']) . "'>" . htmlspecialchars($track['title']) . "</li>";
}

echo <<<HTML
	</ul>
	<script>
		var audioPlayer = document.getElementById('audio-player');
		var playlist = document.getElementById('playlist');
		var tracks = playlist.getElementsByTagName('li');
		var autoplayToggle = document.getElementById('autoplay-toggle');
		var playlistSelect = document.getElementById('playlist-select');
		var playlistNameElement = document.getElementById('playlist-name');
	
		function playTrack(track) {
			var src = track.getAttribute('data-src');
			audioPlayer.src = src;
			audioPlayer.play();
	
			for (var i = 0; i < tracks.length; i++) {
				tracks[i].classList.remove('current-track');
			}
			track.classList.add('current-track');
		}
	
		function loadPlaylist(playlistName) {
			fetch('index.php?playlist=' + playlistName)
				.then(response => response.text())
				.then(html => {
					var parser = new DOMParser();
					var doc = parser.parseFromString(html, 'text/html');
					playlist.innerHTML = doc.getElementById('playlist').innerHTML;
					tracks = playlist.getElementsByTagName('li');
					attachTrackListeners();
					if (tracks.length > 0) {
						playTrack(tracks[0]);
					}
					// Update the playlist name display
					playlistNameElement.textContent = playlistName;
				});
		}
	
		function attachTrackListeners() {
			for (var i = 0; i < tracks.length; i++) {
				tracks[i].addEventListener('click', function() {
					playTrack(this);
				});
			}
		}
	
		playlistSelect.addEventListener('change', function() {
			if (this.value) {
				loadPlaylist(this.value);
			}
		});
	
		audioPlayer.addEventListener('ended', function() {
			if (autoplayToggle.checked) {
				var currentTrack = document.querySelector('.current-track');
				var nextTrack = currentTrack.nextElementSibling;
				if (nextTrack == null) {
					nextTrack = playlist.getElementsByTagName('li')[0];
				}
				playTrack(nextTrack);
			}
		});
	
		attachTrackListeners();
	
		// Play the first track when the page loads
		if (tracks.length > 0) {
			playTrack(tracks[0]);
		}
	</script>
	
</body>
</html>
HTML;
?>