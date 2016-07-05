<!DOCTYPE html>
<html>
	<head>
		<title>Main</title>
		<link rel="stylesheet" href="css/main.css" type="text/css">
		<link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
		<link href='https://fonts.googleapis.com/css?family=Alegreya+Sans:400,500' rel='stylesheet' type='text/css'>
		<link href='https://fonts.googleapis.com/css?family=Raleway' rel='stylesheet' type='text/css'>
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
	if (empty($_GET)){
		require_once ('config.php');
		$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		$albums = $mysqli->query("SELECT * FROM albums");
		while ( $row = $albums->fetch_assoc() ) {
			$album_id = $row['album_id' ];
			$source = [];
			$thumbnail = $mysqli->query("SELECT * FROM albums_images WHERE album_id = $album_id");
			while ($src = $thumbnail ->fetch_assoc()){
				$img = $src['img_id'];
				$thumb = $mysqli->query("SELECT * FROM images WHERE img_id = $img");
				while ($test = $thumb->fetch_assoc()){
					$source[] = $test['file_path'];
				}
			}
			if (empty($source)){
				$source[] = "https://i.vimeocdn.com/portrait/1274237_300x300.jpg";
			}
		 	print ( "<div class = 'album'>" );
		 	print ( "<div class = 'left'>" );
		 	print ( "<img class = 'photo' alt = '$album_id' src ='$source[0]'>" ); //
		 	print ( "</div>" );
		 	print ( "<div class = 'right'>" );
		 	print ( " <h4> <a class='album_link' href='albums.php?album_id=$album_id'>Title: {$row['title' ]}</a><br>" );
		 	print ( "Created On: {$row[ 'date_created' ]} </h4> <br>" );  
		 	print ( "</div>" );
		 	print ( "</div><br>" );
		}
	}
	else {
		require_once ('config.php');
		$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		$photos = $mysqli->query("SELECT * FROM albums_images
									LEFT OUTER JOIN albums
										ON albums.album_id = albums_images.album_id
									LEFT OUTER JOIN images
										ON images.img_id = albums_images.img_id"
								);

		while ( $row = $photos->fetch_assoc() ) {
			$album_id = $row['album_id'];
			$img_id = $row['img_id'];
			$url = $_GET['album_id'];
			if ($url[0] == $row['album_id']){
				print ( " <div class = 'albumphotowrapper'> ");
				print ( " <a href='search.php?img_id=$img_id'> <img class = 'albumphoto' alt = '{$row[ 'file_name' ]}' src='{$row[ 'file_path' ]}'> </a><br> " );
			 	print ( " <h2> {$row[ 'caption' ]} </h2> " );
			 	print ( " <h3> Credit: {$row[ 'credit' ]} <br> " );
			 	print ( " Taken On: {$row[ 'date_' ]}  <br> " );
			 	print ( " ID: $img_id </h3><br>");
			 	print ( " </div> ");
			}	
		}
	}
	?>

	


	</div>
</body>