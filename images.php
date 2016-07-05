<!DOCTYPE html>
<html>
	<head>
		<title>Main</title>
		<link rel="stylesheet" href="css/main.css" type="text/css">
		<link href='https://fonts.googleapis.com/css?family=Raleway' rel='stylesheet' type='text/css'>
		<link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
		<link href='https://fonts.googleapis.com/css?family=Alegreya+Sans:400,500' rel='stylesheet' type='text/css'>
	</head>

<body>
	<div class="navigation">
		<ul>
			<li class = "selected"><a href="index.php">Home</a></li>
			<li><a href="images.php">Images</a></li>
			<li><a href="albums.php">Albums</a></li>
			<li><a href="add.php">Modify</a></li>
			<li><a href="search.php">Search</a></li>
			<li style="float:right;list-style-type:none;"> <a class="active" href="#about"></a></li>
		</ul>
	</div>

	<div class="container">


		<?php
		require_once ('config.php');
		$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		$images = $mysqli->query("SELECT * FROM images");
		while ( $row = $images->fetch_assoc() ) {
			$img_id = $row['img_id'];
			print ( " <div class = 'wrapper'> ");
			print ( " <a href='search.php?img_id=$img_id'> <img class = 'photo2' alt='{$row['file_name']}' src='{$row[ 'file_path' ]}'> </a><br> " );
		 	print ( " <h2> {$row[ 'caption' ]} </h2> " );
		 	print ( " <h3> Credit: {$row[ 'credit' ]} <br> " );
		 	print ( " Taken On: {$row[ 'date_' ]}  <br> " );
		 	print ( " ID: {$row[ 'img_id' ]} </h3><br> " );
		 	print ( " </div> ");
		}		
		$mysqli->close();
		
		?>





	</div>
</body>