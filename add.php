<?php session_start(); ?>
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
		if (isset($_SESSION['logged_user'])){
			echo '
			<h1> Add an Album </h1>
			<form method="POST" class="albumForm">
				Album Title: 
				<input type="text" name="title">
				Date Created (YYYY-MM-DD): 
				<input type="text" name="created">
				Date Modified (YYYY-MM-DD):      
				<input type="text" name="modified">
				<br><br>
				<center><input type="submit" name="submit" value="Add Album"></center>
			</form>
			';

			//Album Submit
			if(isset($_POST['submit'])) {
				$title = $_POST['title'];
		    	$created = $_POST['created'];
		    	$modified = $_POST['modified'];
		    	$execute = true;
		    	if (empty($title)|| (strlen($title)>= 60)|| !preg_match('/^[a-z0-9\040\.\!\:\-]+$/i', $title)){
		    		print("Please enter a valid title.<br>");
		    		$execute = false;
		    	}
		    	if (empty($created)|| !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$created)){
		    		print("Please enter a valid date created with this format YYYY-MM-DD.<br>");
		    		$execute = false;
		    	}
		    	if (empty($modified)|| !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$modified)){
		    		print("Please enter a valid date modified with this format YYYY-MM-DD.<br>");
		    		$execute = false;
		    	}
		    	if ($execute == true){
		    		require_once ('config.php');
					$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
					if (!$mysqli){
						die('Could not connect: ' . mysql_error());
					}
			    	$sql = "INSERT INTO albums (title, date_created, date_modified) VALUES ('$title', '$created', '$modified');";
			    	//print($sql);
			    	if( $mysqli->query( $sql ) ) {
						$new_id = $mysqli->insert_id;
					}
					if ( $mysqli->errno ) {
						print($mysqli->error);
						exit();
					}
					print("This album has been added!");
		    	}
			}
	
			echo '
			<h1> Add an Image </h1>
			<form method="POST" class="imgForm" enctype="multipart/form-data">
				Upload:
				<input type="file" name="newphoto"><br>
				Caption: 
				<input type="text" name="caption">
				Credit:  
				<input type="text" name="credit">
				Date Taken (YYYY-MM-DD):  
				<input type="text" name="taken">
				<br>
				Add to: ';
					require_once ('config.php');
					$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
					if (!$mysqli){
						die('Could not connect: ' . mysql_error());
					}
					$album_ids = $mysqli->query("SELECT * FROM albums");
					while ($id = $album_ids -> fetch_assoc()){
						$a_id = $id['album_id'];
						$title = $id['title'];
						print("<label>$title</label><input type='checkbox' name='albums[]' value='$a_id'> ");
					
					}
					if ( $mysqli->errno ) {
						print($mysqli->error);
						exit();
					}
				echo '
				<center><input type="submit" name="img_submit" value="Add Image"></center>
			</form>
			';

			//Image Submit
			if (isset($_POST['img_submit'])){
				$execute = true;

				$caption = $_POST["caption"];
				$credit = $_POST["credit"];
				$taken = $_POST['taken'];

				$newFile = $_FILES['newphoto'];
				if (empty($newFile)){
					print ('Please upload an image. <br>');
					$execute = false;
				}
				if (empty($taken)|| !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$taken)){
		    		print("Please enter a valid date taken with this format YYYY-MM-DD.<br>");
		    		$execute = false;
		    	}
		    	if (empty($caption) || (strlen($caption)>= 100 || !preg_match('/^[a-z0-9\040\.\!\:\-]+$/i', $caption))){
					print ('Please enter a valid caption.<br>');
					$execute = false;
				}
				if (empty($credit)|| (strlen($credit)>= 50) || !preg_match('/^[a-z\040\-]+$/i', $credit)){
					print ('Please enter a credit name under 50 characters.<br>');
					$execute = false;
				}
				$originalName = $newFile['name'];
				$tempName = $newFile['tmp_name'];
				$size_in_bytes = $newFile['size'];
				$type = $newFile['type'];
				$error = $newFile['error'];
				
				if ($execute == true){
					move_uploaded_file($tempName, "images/$originalName");
		    		require_once ('config.php');
					$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
					if (!$mysqli){
						die('Could not connect: ' . mysql_error());
					}
			    	$sql = "INSERT INTO images (caption, file_path, file_name, credit, date_) 
			    			VALUES ('$caption', 'images/$originalName', '$originalName', '$credit', '$taken');";
			    	
			    	if( $mysqli->query( $sql ) ) {
						$new_id = $mysqli->insert_id;
					}
					if ( $mysqli->errno ) {
						print($mysqli->error);
						exit();
					}
					$find_photo = $mysqli -> query ("SELECT * FROM images");
					$photo_id;
					while ($row = $find_photo->fetch_assoc()){
						$img_id = $row['img_id'];
						$file_path = $row['file_path'];
						if ($file_path == "images/$originalName"){
							$photo_id = $img_id;
						}
						//print($photo_id);
					}
					$albums = $_POST['albums'];
		    		if(!empty($albums)){
		    			foreach ($albums as $num){
		    				$sql = "INSERT INTO albums_images (album_id, img_id) VALUES ('$num','$photo_id');";
		    				$mysqli->query($sql);
		    				if ( $mysqli->errno ) {
								print($mysqli->error);
								exit();
							}
		    			}
		    		}
		    		print("This image has been added!");
		    	}

			}

			echo '
			<h1> Edit An Album </h1>
			<form method="POST" class="albumForm">
				Pick an Album to Edit: ';
				require_once ('config.php');
					$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
					if (!$mysqli){
						die('Could not connect: ' . mysql_error());
					}
					$album_ids = $mysqli->query("SELECT * FROM albums");
					while ($id = $album_ids -> fetch_assoc()){
						$a_id = $id['album_id'];
						$title = $id['title'];
						print("<label>$title</label><input type='radio' name='album' value='$a_id'> ");
					
					}
					if ( $mysqli->errno ) {
						print($mysqli->error);
						exit();
					}
				echo ' 
				<br>
				Title: 
				<input type="text" name="title"><br>
				Date Created (YYYY-MM-DD): 
				<input type="text" name="created"><br>
				Date Modified (YYYY-MM-DD):      
				<input type="text" name="modified"><br>
				Add a Photo:
				<input type="number" name="addPhoto"><br>
				Delete a Photo:
				<input type="number" name="deletePhoto">
				<br><br>
				<center><input type="submit" name="edit_album_submit" value="Edit Album"></center>
			</form>
			';
			//Edit Album
			if (isset($_POST['edit_album_submit'])){
				$album = $_POST['album'];
		    	$created = $_POST['created'];
		    	$modified = $_POST['modified'];
		    	$add_id = $_POST['addPhoto'];
		    	$delete_id = $_POST['deletePhoto'];
		    	$title = $_POST['title'];
		    	$execute = true;
		    	$queries = [];
		    	if (empty($album)){
		    		print("Please select an album to edit.<br>");
		    		$execute = false;
		    	}
		    	if (!empty($title) && ((strlen($title)>= 60) || !preg_match('/^[a-z0-9\040\-]+$/i', $title))){
		    		print("Please enter a valid title with 60 or less characters");
		    		$execute = false;
		    	}
		    	else if (!empty($title)) {
		    		$queries[] = "UPDATE albums SET title = '$title' WHERE album_id = '$album';";
		    	}

		    	if (!empty($created) && !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$created)){
		    		print("Please enter a valid date created with this format YYYY-MM-DD.<br>");
		    		$execute = false;
		    	}
		    	else if (!empty($created)){
		    		$queries[] = "UPDATE albums SET date_created = '$created' WHERE album_id = '$album';";
		    	}

		    	if (!empty($modified) && !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$modified)){
		    		print("Please enter a valid date modified with this format YYYY-MM-DD.<br>");
		    		$execute = false;
		    	}
		    	else if (!empty($modified)) {
		    		$queries[] = "UPDATE albums SET date_modified = '$modified' WHERE album_id = '$album';";
		    	}

		    	if (!empty($add_id) && !preg_match("/^[1-9][0-9]*$/" ,$add_id)){
		    		print("Please enter a valid photo id for adding a photo.");
		    		$execute = false;
		    	}
		    	else if (!empty($add_id)) {
		    		$queries[] = "INSERT INTO albums_images (album_id, img_id) VALUES ('$album', '$add_id');";
		    	}

		    	if (!empty($delete_id) && !preg_match("/^[1-9][0-9]*$/",$delete_id)){
		    		print("Please enter a valid photo id for deleting a photo.");
		    		$execute = false;
		    	}
		    	else if (!empty($delete_id)){
		    		$queries[] = "DELETE FROM albums_images WHERE album_id = '$album' AND img_id = '$delete_id';";
		    	}

		    	if ($execute == true){
		    		require_once ('config.php');
					$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
					if (!$mysqli){
						die('Could not connect: ' . mysql_error());
					}
					if (empty($queries)){
						print("You did not enter any fields to edit!");
					}
					else {
						foreach ($queries as $query){
							$mysqli->query($query);
							if ($mysqli->errno){
								print($mysqli->error);
								exit();
							}
							print ("Album has been successfully edited!");
						}
					}
		    	}
			}
			echo '
			<h1> Edit an Image </h1>
			<form method="POST" class="albumForm">
				Image ID: 
				<input type="number" name="img_id"><br>
				Caption:
				<input type="text" name="caption"><br>
				Credit: 
				<input type="text" name="credit"><br>
				Date Taken (YYYY-MM-DD):      
				<input type="text" name="taken">
				<br><br>
				<center><input type="submit" name="edit_img_submit" value="Edit Image"></center>
			</form>
			';
			//Edit Image
			if (isset($_POST['edit_img_submit'])){
				$img_id = $_POST['img_id'];
		    	$caption = $_POST['caption'];
		    	$taken = $_POST['taken'];
		    	$credit = $_POST['credit'];
		    	$execute = true;
		    	//$queries = [];
		    	if ($img_id == null){
		    		print("Please enter an image that you would like to edit.<br>");
		    		$execute = false;
		    	}
		    	if (!empty($taken) && !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $taken)){
		    		print("Please enter a valid date taken with this format YYYY-MM-DD.<br>");
		    		$execute = false;
		    	}
		    	if (!empty($caption) && ((strlen($caption)>= 100) || !preg_match('/^[a-z0-9\040\.\!\:\-]+$/i', $caption))){
					print ('Please enter a valid caption under 100 characters.<br>');
					$execute = false;
				}
				if (!empty($credit) && ((strlen($credit)>= 50) || !preg_match('/^[a-z0-9\040\-]+$/i', $credit))){
					print ('Please enter a valid credit name under 50 characters.<br>');
					$execute = false;
				}
				if ($execute == true){
					require_once ('config.php');
					$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
					if (!$mysqli){
						die('Could not connect: ' . mysql_error());
					}
					if(!empty($taken)){
						$query = "UPDATE images SET date_ = '$taken' WHERE img_id = '$img_id';";
						$mysqli->query($query);
						if ($mysqli->errno){
							print($mysqli->error);
							exit();
						}
					}
					if(!empty($caption)){
						$query = "UPDATE images SET caption = '$caption' WHERE img_id = '$img_id';";
						$mysqli->query($query);
						if ($mysqli->errno){
							print($mysqli->error);
							exit();
						}
					}
					if(!empty($credit)){
						$query = "UPDATE images SET credit = '$credit' WHERE img_id = '$img_id';";
						$mysqli->query($query);
						if ($mysqli->errno){
							print($mysqli->error);
							exit();
						}

					}
					
					print("Image has been successfully edited!");
				}
			}

			echo '
				<h1> Delete an Album </h1>
				<form method="POST" class="albumForm"> ';
					require_once ('config.php');
					$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
					if (!$mysqli){
						die('Could not connect: ' . mysql_error());
					}
					$album_ids = $mysqli->query("SELECT * FROM albums");
					while ($id = $album_ids -> fetch_assoc()){
						$a_id = $id['album_id'];
						$title = $id['title'];
						print("<label>$title</label><input type='radio' name='album' value='$a_id'> ");
					
					}
					if ( $mysqli->errno ) {
						print($mysqli->error);
						exit();
					}

				echo '<center><input type="submit" name="delete_album" value="Delete Album"></center>
						</form>';
				//Delete Album
				if (isset($_POST['delete_album'])){
					$album_id = $_POST['album'];
					require_once ('config.php');
					$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
					if (!$mysqli){
						die('Could not connect: ' . mysql_error());
					}
					$query1 = "DELETE FROM albums WHERE album_id = '$album_id';";
					$query2 = "DELETE FROM albums_images WHERE album_id = '$album_id';";
					$mysqli->query($query1);
					$mysqli->query($query2);
					if ($mysqli->errno){
						print($mysqli->error);
						exit();
					}
					print("Album has been deleted!");
				}

			echo '
				<h1> Delete an Image </h1>
				<form method = "POST">
					Image ID: 
					<input type = "number" name = "img_id"><br>
					<br><br>
					<center><input type="submit" name="delete_img" value="Delete Image"></center>
				</form>	
			';
			//Delete Image
			if (isset($_POST['delete_img'])){
				$img_id = $_POST['img_id'];
				require_once ('config.php');
				$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
				if (!$mysqli){
					die('Could not connect: ' . mysql_error());
				}
				$query1 = "DELETE FROM images WHERE img_id = '$img_id';";
				$query2 = "DELETE FROM albums_images WHERE img_id = '$img_id';";
				
					$mysqli->query($query1);
					$mysqli->query($query2);
					if ($mysqli->errno){
						print($mysqli->error);
						print ('There is no image with this id.');
						exit();
					}
					print("Image has been deleted!");
			}
		}
		else {
			echo 'You must be logged in to access the functions of this page. Click <a href= "index.php">here</a> to log in.';
		}

	?>



	</div>
</body>