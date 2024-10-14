<?php
session_start();

// Set your password here
$correct_password = "dethlord";

// Check if user is logged in
$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
	if ($_POST['password'] === $correct_password) {
		$_SESSION['logged_in'] = true;
		$is_logged_in = true;
	} else {
		$login_error = "Incorrect password. Please try again.";
	}
}

// Handle logout
if (isset($_GET['logout'])) {
	session_unset();
	session_destroy();
	header("Location: builder");
	exit();
}

// The rest of your existing code goes here, but wrap it in a condition to check if user is logged in
if ($is_logged_in) {
	// Initialize variables
	$data = [];
	$editMode = false;
	$selectedFile = '';

	// Function to sanitize filename
	function sanitizeFilename($filename) {
		return basename($filename);
	}

	// Handle file deletion
	if (isset($_POST['deleteFile']) && !empty($_POST['fileToDelete'])) {
		$fileToDelete = sanitizeFilename($_POST['fileToDelete']);
		if (file_exists($fileToDelete) && unlink($fileToDelete)) {
			$message = "File $fileToDelete has been deleted successfully.";
		} else {
			$message = "Error deleting file $fileToDelete.";
		}
	}

	// Check if a file is selected for editing
	if (isset($_POST['selectFile']) && !empty($_POST['existingFile'])) {
		$selectedFile = sanitizeFilename($_POST['existingFile']);
		$editMode = true;
		if (file_exists($selectedFile)) {
			$jsonContent = file_get_contents($selectedFile);
			$data = json_decode($jsonContent, true);
		}
	}

	// Check if the form is submitted for saving
	if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['saveChanges'])) {
		// Get the filename from the form
		$filename = !empty($_POST['filename']) ? sanitizeFilename($_POST['filename']) : 'output.json';
		
		// Ensure the filename ends with .json
		if (!str_ends_with(strtolower($filename), '.json')) {
			$filename .= '.json';
		}
		
		// Process form data
		$data = [];
		for ($i = 0; isset($_POST["title$i"]) && isset($_POST["url$i"]); $i++) {
			$title = trim($_POST["title$i"]);
			$url = trim($_POST["url$i"]);
			
			if (!empty($title) || !empty($url)) {
				$data[] = [
					"title" => $title,
					"url" => $url
				];
			}
		}
		
		// Convert the data to JSON
		$json_data = json_encode($data, JSON_PRETTY_PRINT);
		
		// Write the JSON data to the file
		if (file_put_contents($filename, $json_data) !== false) {
			$message = "JSON file saved successfully as: " . $filename;
		} else {
			$message = "Error saving JSON file.";
		}
	}

	// Get list of JSON files in the current directory
	$jsonFiles = glob("*.json");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="robots" content="noindex, nofollow">
	<title>Playlist Builder</title>
	<style>
		body {
			font-family: "Nunito", sans-serif;
			font-optical-sizing: auto;
			font-weight: 300;
			font-style: normal;
			margin: 0;
			padding: 0;
			color: #fff;
			font-size: 16px;
			background-color: #000;
			display: flex;
			justify-content: center;
			align-items: center;
			flex-direction: column;
		}
		
		.login-box{
			display: flex;
			justify-content: center;
			align-items: center;
			flex-direction: column;
			height: 100vh;
			& .login-bg{
				display: flex;
				flex-direction: column;
				justify-content: center;
				align-items: flex-start;
				gap:10px;
				background-color: #333;
				padding: 20px;
				border-radius: 6px;
				& form{
					display:flex;
					gap:10px;
				}
			}
		}
		
		h1{
			font-weight: 300;
			margin: 0;
			padding: 0;
			font-size: 22px;
		}
		.header-titles{
			max-width: 906px;
			display: flex;
			width: 100%;
			justify-content: space-between;
			align-items: center;
			gap: 10px;
			margin:  40px 0 20px 0;
			.button-group{
				& a{
					text-decoration: none;
					height: 40px;
					background-color: #336699;
					display:flex;
					justify-content: center;
					align-items: center;
					border-radius: 6px;
					padding: 0 10px 0 10px;
				}
				
				.logout-btn{
					background-color: red;
				}
			}
		}
		.top-form {
			width: 100%;
			max-width: 900px;
			display: flex;
			gap: 10px;
			flex-direction: column;
			margin-bottom: 30px;
		}
		
		#input-container {
			flex-direction: column;
			display: flex;
			min-width: 900px;
			box-sizing: border-box;
			justify-content: start;
			align-items: start;
			gap: 5px;
			margin: 0 0 5px 0;
		}
		
		#input-container .show-entry {
			width: 100%;
			display: flex;
			gap: 5px;
			align-items: center;
			justify-content: center;
		}
		
		#input-container input {
			border-radius: 6px;
			width: 45%;
			color: #fff;
			box-sizing: border-box;
		}
		
		.button-group{
			display: flex;
			gap:10px;
		}
		
		a { color: #fff; }
		
		input, select, button {
			color: #fff;
			font-family: 'Trebuchet MS', sans-serif;
			font-size: 16px;
			padding: 10px;
			background-color: #333;
			border: none;
			border-radius: 6px;
		}
		
		.pw-field {
			padding: 10px;
			background-color: #000;
			color: #fff;
		}
		
		.pw-btn {
			padding: 10px;
			background-color: #000;
			color: #fff;
		}
		
		.submit-file-btn{
			cursor: pointer;
			background-color: #336699 !important;
		}
		
		.arrow-container {
			display: flex;
			flex-direction: column;
			margin-right: 5px;
			width: 20px !important;
		}
		
		.arrow {
			cursor: pointer;
			user-select: none;
			font-size: 12px;
			color: #fff;
			background: none;
			border: none;
			padding: 0;
			margin: 0;
		}
		
		.arrow:hover {
			color: #4CAF50;
		}
		
		.arrow:active {
			color: #45a049;
		}
		
		.remove {
			background-color: #ff4d4d;
			color: white;
			border: none;
			padding: 5px 10px;
			cursor: pointer;
			border-radius: 50%;
			width: 38px;
			height: 38px;
			margin-top: 1px;
		}
		
		.remove:hover {
			background-color: #ff1a1a;
		}

		.delete-btn {
			background-color: #ff4d4d;
			color: white;
			border: none;
			padding: 10px;
			cursor: pointer;
			border-radius: 4px;
		}
		.delete-btn:hover {
			background-color: #ff1a1a;
		}
		
		.delete-head{
			text-decoration: none;
			height: 40px;
			background-color: green;
			display: flex;
			justify-content: center;
			align-items: center;
			border-radius: 6px;
			padding: 0 10px 0 10px;
			cursor: pointer;
		}
		.delete-body{
			gap: 10px;
			align-items: center;
		}
	</style>
</head>
<body>
	
	<?php if (!$is_logged_in): ?>
		<div class="login-box">
			<div class="login-bg">
				<h1>Radio Builder</h1>
				<!-- Login Form -->
				<form method="post">
					<input class="pw-field" type="password" id="password" placeholder="Password" name="password" required>
					<input class="pw-btn" type="submit" value="Login">
				</form>
			</div>
		</div>
		<?php if (isset($login_error)): ?>
			<p style="color: red;"><?php echo $login_error; ?></p>
		<?php endif; ?>
	<?php else: ?>
		<!-- Logout Link -->
		<div class="header-titles"><h1>Radio Builder</h1><div class="button-group"><a href="/radio">View Stations</a><div class="delete-head">Delete Playlists</div><a class="logout-btn" href="?logout=1">Logout</a></div></div>
		
		<form class="top-form" method="post" onsubmit="return confirm('Are you sure you want to delete this file?');">
			<div class="delete-body" style="display:none;">
				<select name="fileToDelete" id="fileToDelete">
					<option value="">Select a file</option>
					<?php foreach ($jsonFiles as $file): ?>
						<option value="<?php echo htmlspecialchars($file); ?>">
							<?php echo htmlspecialchars($file); ?>
						</option>
					<?php endforeach; ?>
					<input class="delete-btn" type="submit" name="deleteFile" value="Delete File">
				</select>
			</div>
		</form>
		
		<!-- File Selection Form -->
		<form class="top-form" method="post">
			<label for="existingFile">Select existing file to edit:</label>
			<select name="existingFile" id="existingFile">
				<option value="">Select a file</option>
				<?php foreach ($jsonFiles as $file): ?>
					<option value="<?php echo htmlspecialchars($file); ?>" <?php echo ($file === $selectedFile) ? 'selected' : ''; ?>>
						<?php echo htmlspecialchars($file); ?>
					</option>
				<?php endforeach; ?>
			</select>
			<input class="submit-file-btn" type="submit" name="selectFile" value="Load File">
		</form>

		<!-- Delete File Form -->
		

		<!-- Main Edit Form -->
		<form method="post" id="playlistForm">
			<label for="filename">Output Filename:</label>
			<input type="text" id="filename" name="filename" placeholder="Enter filename (e.g., output.json)" value="<?php echo htmlspecialchars($selectedFile ?: 'output.json'); ?>">
			<br><br>
			<div id="input-container">
				<?php if ($editMode && !empty($data)): ?>
					<?php foreach ($data as $index => $item): ?>
						<div class="show-entry">
							<div class="arrow-container">
								<button type="button" class="arrow" onclick="moveUp(this)">&#9650;</button>
								<button type="button" class="arrow" onclick="moveDown(this)">&#9660;</button>
							</div>
							<input type="text" name="title<?php echo $index; ?>" placeholder="Title" value="<?php echo htmlspecialchars($item['title']); ?>">
							<input type="text" name="url<?php echo $index; ?>" placeholder="URL" value="<?php echo htmlspecialchars($item['url']); ?>">
							<button type="button" class="remove" onclick="removeItem(this)">-</button>
						</div>
					<?php endforeach; ?>
				<?php else: ?>
					<div class="show-entry">
						<div class="arrow-container">
							<button type="button" class="arrow" onclick="moveUp(this)">&#9650;</button>
							<button type="button" class="arrow" onclick="moveDown(this)">&#9660;</button>
						</div>
						<input type="text" name="title0" placeholder="Title">
						<input type="text" name="url0" placeholder="URL">
						<button type="button" class="remove" onclick="removeItem(this)">-</button>
					</div>
				<?php endif; ?>
			</div>
			<button type="button" onclick="addField()">+ Ad Audio</button>
			<input type="submit" name="saveChanges" value="Save">
		</form>
		<?php if (isset($message)) echo "<p>$message</p>"; ?>
	<?php endif; ?>

	<script>
		function addField() {
			var container = document.getElementById('input-container');
			var index = container.children.length;
			var newFields = document.createElement('div');
			newFields.className = 'show-entry';
			newFields.innerHTML = `
				<div class="arrow-container">
					<button type="button" class="arrow" onclick="moveUp(this)">&#9650;</button>
					<button type="button" class="arrow" onclick="moveDown(this)">&#9660;</button>
				</div>
				<input type="text" name="title${index}" placeholder="Title">
				<input type="text" name="url${index}" placeholder="URL">
				<button type="button" class="remove" onclick="removeItem(this)">-</button>
			`;
			container.appendChild(newFields);
		}

		function moveUp(button) {
			var div = button.closest('div').parentNode;
			var prevDiv = div.previousElementSibling;
			if (prevDiv) {
				div.parentNode.insertBefore(div, prevDiv);
			}
		}

		function moveDown(button) {
			var div = button.closest('div').parentNode;
			var nextDiv = div.nextElementSibling;
			if (nextDiv) {
				div.parentNode.insertBefore(nextDiv, div);
			}
		}

		function removeItem(button) {
			var div = button.closest('div');
			div.parentNode.removeChild(div);
		}

		// Renumber the input fields before form submission
		document.getElementById('playlistForm').onsubmit = function() {
			var container = document.getElementById('input-container');
			var inputs = container.getElementsByTagName('input');
			for (var i = 0; i < inputs.length; i++) {
				var input = inputs[i];
				var newName = input.name.replace(/\d+/, Math.floor(i/2));
				input.name = newName;
			}
			return true;
		};
		// Remove Playlists from server
		document.addEventListener('DOMContentLoaded', function () {
			const deleteHead = document.querySelector('.delete-head');
			const deleteBody = document.querySelector('.delete-body');
		
			deleteHead.addEventListener('click', function () {
				if (deleteBody.style.display === 'none' || deleteBody.style.display === '') {
					deleteBody.style.display = 'flex';
				} else {
					deleteBody.style.display = 'none';
				}
			});
		});
		
	</script>
	
</body>
</html>