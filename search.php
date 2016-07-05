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
		<h1> SEARCH through captions, credits, and file names</h1>
		<form method="POST" class="searchForm" action="search.php">
			<input type="text" name="search" value="<?php if (isset($search)) echo $search; ?>">
			<input type="submit" name="submit" value="Click to Submit">
			<button type="submit">Clear</button>
		</form>
		
		
		<br><br>
		<?php
			if (empty($_GET)){
				if(isset($_POST['submit'])) {
					$query = $_POST['search'];
					if (!preg_match('/^[a-z0-9\040\.\!\:\-]+$/i', $query)){
						print ("Please enter a valid search query!");
					}
					else {
						$search = strtolower($_POST["search"]);
						require_once ('config.php');
						$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
						$images = $mysqli->query("SELECT * FROM images");
						$results = [];
						while ( $row = $images->fetch_assoc() ) {
							$img_id = $row['img_id'];
							$caption = strtolower($row['caption']);
							$credit = strtolower($row['credit']);
							$title = strtolower($row['file_name']);
							if ((strpos($caption, $search) !== false) || (strpos($credit, $search) !== false) || (strpos($title, $search) !== false)){
								print ( " <div class = 'searchwrapper'> ");
								print ( " <a id='img_link' href='search.php?img_id=$img_id'>
										<img class = 'albumphoto' src='{$row[ 'file_path' ]}'></a><br> " );
							 	print ( " </div> ");
							}
						}		
						$mysqli->close();
					}
					
				}
			}

			else {
				require_once ('config.php');
				$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
				$url = $_GET['img_id'];
				$query = "SELECT * FROM images WHERE img_id = $url";
				$image = $mysqli -> query("SELECT * FROM images WHERE img_id = $url");
				while ($row = $image -> fetch_assoc() ){
					print ("<center> <h4>{$row['caption']} </h4>");
					print ("<h4>Credit: {$row['credit']} </h4>");
					print ("<h4>Date Taken: {$row['date_']} </h4>");
					print ("<h4>Albums: ");
					$img_id = $row['img_id'];
					$query1 = "SELECT * FROM albums_images WHERE img_id = $img_id";
					$album = $mysqli -> query($query1);
					$albums = [];
					//print("hi");
					while ($ai = $album -> fetch_assoc()){
						$albums[] = $ai['album_id'];
					}
					$albums = array_unique($albums);

					foreach ($albums as $counter){
						$titles = $mysqli -> query("SELECT * FROM albums WHERE album_id = $counter");
						while ($print = $titles -> fetch_assoc()){
							print(" | ".$print['title']);
						}
					}
					print ("<h4>ID: {$row['img_id']} </h4>");
					print ( "<a id='img_link' href='search.php?img_id=$img_id'>
								<img class = 'imgphoto' src='{$row[ 'file_path' ]}'></a></center><br> " );
				}
			
			}
			
		
		?>





	</div>
</body>