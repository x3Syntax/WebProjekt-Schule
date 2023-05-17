<?php
session_start();

if(!(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true))
{
	header('Location: index.php');
    exit;
} 

if (isset($_POST['submit']))
{ 
    $fileMimes = [
    'text/x-comma-separated-values',
    'text/comma-separated-values',
    'application/octet-stream',
    'application/vnd.ms-excel',
    'application/x-csv',
    'text/x-csv',
    'text/csv',
    'application/csv',
    'application/excel',
    'application/vnd.msexcel',
    'text/plain'
	];


	if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK 
		&& in_array(mime_content_type($_FILES['file']['tmp_name']), $fileMimes, true)) 
	{

		$csvFile = fopen($_FILES['file']['tmp_name'], 'r');
		fgetcsv($csvFile);

		while (($getData = fgetcsv($csvFile, 10000, ",")) !== false) 
		{
			[$pc_name, $ip_address, $submask, $vlan, $gateway] = $getData;
			$stmt = $pdo->prepare('SELECT pc_name FROM devices WHERE pc_name = :email');
			$stmt->execute(['pc_name' => $pc_name]);

			if ($stmt->rowCount() > 0) 
			{
				$stmt = $pdo->prepare('UPDATE devices SET pc_name = :name, created_at = NOW() WHERE email = :email');
				$stmt->execute(['name' => $name, 'email' => $email]);
			} 
			else 
			{
				$stmt = $pdo->prepare('INSERT INTO devices (pc_name, ip_address, submask, vlan, gateway) VALUES (:name, :email, NOW(), NOW())');
				$stmt->execute();
			}
		}
		fclose($csvFile);
		header("Location: index.php");
	} 
	else 
	{
		echo "Please select a valid file.";
	}
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
		  <button class="w3-bar-item w3-button w3-hide-large w3-hover-none w3-hover-text-light-grey" onclick="w3_open();"><i class="fa fa-bars"></i></button>
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
			<a href="import.php" class="w3-bar-item w3-button w3-padding w3-blue"><i class="fa fa-users fa-fw"></i>  Import Geräte</a>
			<a href="export.php" class="w3-bar-item w3-button w3-padding"><i class="fa fa-bullseye fa-fw"></i>  Export Geräte</a>
			<a href="logout.php" class="w3-bar-item w3-button w3-padding w3-red"><i class="fa fa-bullseye fa-fw"></i>  Logout</a>
		  </div>
		</nav>
		
		<div class="w3-main" style="margin-left:20%; margin-top: 3%;">
		
			<form action="upload.php" method="post" enctype="multipart/form-data">
                <div>
                    <div>
                        <input type="file" id="customFileInput" name="file">
                        <label for="customFileInput"></label>
                    </div>
                    <div>
                        <input class="w3-button w3-dark-grey" type="submit" name="submit" value="Upload" class="btn btn-primary">
                    </div>
                </div>
            </form>
		
		</div>
	</body>
</html>

