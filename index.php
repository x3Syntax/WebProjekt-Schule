<?php
include './db.php';
session_start();

if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) 
{
	header('Location: dashboard.php');
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
	
		<!-- Sidebar/menu -->
		<nav class="w3-sidebar w3-light-grey" style="z-index:3;width:300px;" id="mySidebar"><br>
		  <div class="w3-container w3-row">
			<div class="w3-col s8 w3-bar">
			  <span>Geräte Übersicht</span><br>
			</div>
		  </div>
		  <hr>
		  <div class="w3-bar-block">
			<a href="login.php" class="w3-bar-item w3-button w3-padding w3-green"><i class="fa fa-bullseye fa-fw"></i>  Login</a>
		  </div>
		</nav>
	
		<div class="w3-main" style="margin-left:20%; margin-top: 3%;">
			<div class="w3-main" style="margin-top: 3%;">
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