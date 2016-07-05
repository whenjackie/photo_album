<?php session_start(); 
	if (isset($_SESSION['logged_user'])) {
		$olduser = $_SESSION['logged_user'];
	} 
	else {
		$olduser = false;
	}
?>
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
		if ($olduser){
			echo "You are already logged in! Would you like to log out?";
			echo'<form method="POST">
					<br><input type="submit" name = "logout" value="Log Out">
				</form>
				';

		}
		else {
			echo '
			<h4>Log in</h4>
			<form method="POST">
				Username: <input type="text" name="username"> <br>
				Password: <input type="password" name="password"> <br>
				<input type="submit" name = "submit" value="Submit">
			</form>
			';
		}
	?>
	
	<?php
		if(isset($_POST["logout"])){
			unset( $_SESSION['logged_user'] );
			print( "<p>You have been signed out, $olduser!</p>");
			$olduser = false;
		}

		if(isset($_POST['submit'])){
			$username = $_POST['username'];
			$password = $_POST['password'];
			$filter_username = filter_input( INPUT_POST, 'username', FILTER_SANITIZE_STRING );
			$filter_password = filter_input( INPUT_POST, 'password', FILTER_SANITIZE_STRING );
			$valid_pass = password_verify($filter_password, '$2y$10$pg20oQdJzNbMuNgUCJprQu1qnvGDFm5/XlK8GoL2vDKelst8WvVt2');
			$hash_pass = password_hash ($filter_password, PASSWORD_DEFAULT);
			require_once ('config.php');
			$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
			$query =  "SELECT * FROM Users WHERE username = '$filter_username';";
			if( $mysqli->query( $query ) ) {
				$result = $mysqli->query($query);
				if ($result && $result->num_rows == 1) {
					$row = $result->fetch_assoc();
					$db_password = $row[ 'hashpassword' ];
					if( password_verify( $filter_password, $db_password )){
						$_SESSION['logged_user'] = $_POST['username'];
						echo "You have successfully logged in. You can now add albums or images.";
					}
					else {
		 				echo "You have not successfully logged in. Please try again.";
		 			}
				}
			}	
			if ( $mysqli->errno ) {
				print($mysqli->error);
				exit();
				}

		}

	?>


	</div>
</body>