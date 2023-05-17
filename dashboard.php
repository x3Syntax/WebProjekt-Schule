<?php
include './db.php';
session_start();

if(!(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true))
{
	header('Location: index.php');
    exit;
} 


//DB connection herstellen
$dbSession = new DBClass();
$dbSession->connect_db();

//Alle Infos der PC´s laden aus der db
$configs = $dbSession->getAllConfigs();

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
		  <button class="w3-bar-item w3-button w3-hide-large w3-hover-none w3-hover-text-light-grey" onclick="w3_open();"><i class="fa fa-bars"></i>  Menussssssssssss</button>
		  <span class="w3-bar-item w3-right">Logo</span>
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
			<a href="dashboard.php" class="w3-bar-item w3-button w3-padding w3-blue"><i class="fa fa-users fa-fw"></i>  Overview</a>
			<a href="users.php" class="w3-bar-item w3-button w3-padding"><i class="fa fa-users fa-fw"></i>  Benutzer verwalten</a>
			<a href="config.php" class="w3-bar-item w3-button w3-padding"><i class="fa fa-eye fa-fw"></i>  Netzgeräte verwalten</a>
			<a href="import.php" class="w3-bar-item w3-button w3-padding"><i class="fa fa-users fa-fw"></i>  Import Geräte</a>
			<a href="export.php" class="w3-bar-item w3-button w3-padding"><i class="fa fa-bullseye fa-fw"></i>  Export Geräte</a>
			<a href="logout.php" class="w3-bar-item w3-button w3-padding w3-red"><i class="fa fa-bullseye fa-fw"></i>  Logout</a>
		  </div>
		</nav>
		
		<div class="w3-main" style="margin-left:20%; margin-top: 3%;">
			<div class="w3-main" style="margin-top: 0%;">
			<table class="w3-table">
				<tr>
					<th>PC Name</th>
					<th>IP Address</th>
					<th>Submask</th>
					<th>VLAN</th>
					<th>Gateway</th>
				</tr>
				<!-- foreach schleife um das array komplett zu Laden/anzeigen -->
				<?php foreach($configs as $config): ?>
				<tr>
					<td><?php echo $config['pc_name']; ?><input type="hidden" name="pc_name" value="<?php echo $config['pc_name']; ?>"></td>
					<td><input class="w3-input" type="text" id="ip_address_<?php echo $config['pc_name']; ?>" value="<?php echo $config['ip_address']; ?>" disabled></td>				
					<td><input class="w3-input" type="text" id="submask_<?php echo $config['pc_name']; ?>" value="<?php echo $config['submask']; ?>" disabled></td>	
					<td><input class="w3-input" type="text" id="vlan_<?php echo $config['pc_name']; ?>" value="<?php echo $config['vlan']; ?>" disabled></td>		
					<td><input class="w3-input" type="text" id="gateway_<?php echo $config['pc_name']; ?>" value="<?php echo $config['gateway']; ?>" disabled></td>	
				</tr>
				<?php endforeach; ?>
			</table>
			</div>
		</div>
		
		
	</body>
</html>