<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: content-type");

include './db.php';

session_start();


if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$sID = $_POST['username'];
	$sPW = $_POST['password'];

	$dbSession = new DBClass();
	$dbSession->connect_db();

	if ($dbSession->check_login_Data($sID,$sPW) != 0)
	{
		$_SESSION['loggedin'] = true;
		$dbSession->clearSession();
	}
	else
	{
		echo '<script type="text/javascript">
			window.onload = function () { alert("Incorrect username and/or password!"); } 
		</script>'; 
		$dbSession->clearSession();
	}

}


if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) 
{
	$dbSession->clearSession();
    header('Location: dashboard.php');
    exit;
} 
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Login</title>
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
		<link href="style.css" rel="stylesheet" type="text/css">
	</head>
	<body class="w3-light-grey">
	
		<!-- Top container -->
		<div class="w3-bar w3-top w3-black w3-large" style="z-index:4">
		  <span class="w3-bar-item w3-right">Projekt</span>
		</div>
	
		<div class="login">
			<h1>Login</h1>
			<form action="login.php" method="post">
				<label for="username">
					<i class="fas fa-user"></i>
				</label>
				<input type="text" name="username" placeholder="Username" id="username" required>
				<label for="password">
					<i class="fas fa-lock"></i>
				</label>
				<input type="password" name="password" placeholder="Password" id="password" required>
				<input type="submit" value="Login">
			</form>
		</div>
	</body>
</html>