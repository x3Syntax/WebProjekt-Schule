<?php
session_start();

if(!(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true))
{
	header('Location: index.php');
    exit;
} 

?>

<!DOCTYPE html>
<html>
	<head>
		<title>Dashboard</title>
		<link href="style.css" rel="stylesheet" type="text/css">
	</head>
	<body>
		<div class="header">
			<h1>Wilkommen</h1>
		</div>
		<div class="nav">
			<div class="nav-item">
				<a href="users.php" class="nav-link">Benutzer verwalten</a>
				<a href="config.php" class="nav-link">Netzgeräte verwalten</a>
				<a href="dynamic.php" class="nav-link">Geräte details</a>
				<a href="overview.php" class="nav-link">overview</a>
				<a href="import.php" class="nav-link">import Geräte</a>
				<a href="export.php" class="nav-link">Export Geräte</a>
			</div>

			<div class="logout-button">
				<a href="logout.php" class="nav-link">Logout</a>
			</div>
		</div>
	</body>
</html>