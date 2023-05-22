<?php
include './db.php';
session_start();

if(!(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true))
{
	header('Location: index.php');
    exit;
} 

if(isset($_POST['sent']))
{
	$dbSession = new DBClass();
	$dbSession->connect_db();
	
	$dbSession->exportCSV();
	
	echo '<script type="text/javascript">
       window.onload = function () { alert("CSV erfolgreich exportiert"); } 
	</script>'; 
}

if(isset($_POST['sent2']))
{
	$dbSession = new DBClass();
	$dbSession->connect_db();
	
	$dbSession->exportXML();
	
	echo '<script type="text/javascript">
       window.onload = function () { alert("XML erfolgreich exportiert"); } 
	</script>'; 
}

?>

<!DOCTYPE html>
<html>
	<head>
		<title>Dashboard</title>
		<link href="style.css" rel="stylesheet" type="text/css">
	</head>
	<body class="w3-light-grey">
		<!-- Top container -->
		<div class="w3-bar w3-top w3-black w3-large" style="z-index:4">
		  <span class="w3-bar-item w3-right">Projekt</span>
		</div>
	
		<!-- Sidebar/menu -->
		<nav class="w3-sidebar w3-light-grey" style="z-index:3;width:300px;" id="mySidebar"><br>
		  <div class="w3-container w3-row">
			<div class="w3-col s8 w3-bar">
			  <span>Willkommen</span><br>
			</div>
		  </div>
		  <hr>
		  <div class="w3-bar-block">
			<a href="dashboard.php" class="w3-bar-item w3-button w3-padding"><i class="fa fa-users fa-fw"></i>  Overview</a>
			<a href="users.php" class="w3-bar-item w3-button w3-padding"><i class="fa fa-users fa-fw"></i>  Benutzer verwalten</a>
			<a href="config.php" class="w3-bar-item w3-button w3-padding"><i class="fa fa-eye fa-fw"></i>  Netzgeräte verwalten</a>
			<a href="import.php" class="w3-bar-item w3-button w3-padding"><i class="fa fa-users fa-fw"></i>  Import Geräte</a>
			<a href="export.php" class="w3-bar-item w3-button w3-padding w3-blue"><i class="fa fa-bullseye fa-fw"></i>  Export Geräte</a>
			<a href="logout.php" class="w3-bar-item w3-button w3-padding w3-red"><i class="fa fa-bullseye fa-fw"></i>  Logout</a>
		  </div>
		</nav>
		
		
		<div class="w3-main" style="margin-left:20%; margin-top: 3%;">
			<div>
				<form method="post" action="export.php">
					<input class="w3-button w3-dark-grey" type="submit" name="sent" value="Export CSV">
					<input class="w3-button w3-dark-grey" type="submit" name="sent2" value="Export XML">
				</form>
			</div>
		</div>
	</body>
</html>