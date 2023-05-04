<?php
include './db.php';

session_start();

//DB connection herstellen
$dbSession = new DBClass();
$dbSession->connect_db();

//Session check ob user eingelogged ist.
if(!(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true))
{
	header('Location: index.php');
    exit;
}

//Listener der die db funktion triggered (onUpdate)
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['config_changes'])) {
    $config_changes = json_decode($_POST['config_changes'], true);
    $dbSession->insertChangesPC($config_changes);
}

//Alle Infos der PC´s laden aus der db
$configs = $dbSession->getAllConfigs();
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Dashboard</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<script>
			//Entfernen des disabled attributs
			function editConfig(config_name) 
			{
				let input_elem = document.getElementById(config_name);
				input_elem.removeAttribute('disabled');
			}
			
			//Alle Inputs das attribute "disabled" hinzufügen
			function disableInputs() 
			{
			  const inputs = document.querySelectorAll('input');
			  
			  inputs.forEach(input => {
				input.setAttribute('disabled', true);
			  });
}
			
			//Änderungen übernhemen (DB)
			function saveChanges() 
			{
				disableInputs();
				
				//leeres Objekt
				let configs = {};
	
				// Finden aller Elemente mit dem Namen "pc_name" und iteriere durch sie
				let pc_names = document.querySelectorAll('input[name="pc_name"]');
				for(let i = 0; i < pc_names.length; i++) 
				{
					//Name des PCs aus dem aktuellen Element
					let pc_name = pc_names[i].value;
					//Neues Objekt hinzufügen, das die aktuellen Werte der Eingabefelder für diesen PC enthält
					configs[pc_name] = {
						'pc_name': pc_name,
						'ip_address': document.getElementById('ip_address_' + pc_name).value,
						'submask': document.getElementById('submask_' + pc_name).value,
						'vlan': document.getElementById('vlan_' + pc_name).value,
						'gateway': document.getElementById('gateway_' + pc_name).value
					};
				}
	
				// Konvertieren in einen JSON-String
				let config_changes = JSON.stringify(configs);
				
				// Erstellen eines neuen FormData-Objekt und hinzufügen des JSON-Strings als "config_changes"
				let form_data = new FormData();
				form_data.append('config_changes', config_changes);
	
				//XMLHttpRequest 
				let xhr = new XMLHttpRequest();
				xhr.open('POST', 'config.php');
				xhr.send(form_data);
				
				alert("Changes applied");
			}
			
		</script>
	</head>
	<body>
		<div class="header">
			<h1>Wilkommen</h1>
		</div>
		
		<div class="configMain">
			<table>
				<tr>
					<th>PC Name</th>
					<th>IP Address</th>
					<th>Submask</th>
					<th>VLAN</th>
					<th>Gateway</th>
					<th>Action</th>
				</tr>
				<!-- foreach schleife um das array komplett zu Laden/anzeigen -->
				<?php foreach($configs as $config): ?>
				<tr>
					<td><?php echo $config['pc_name']; ?><input type="hidden" name="pc_name" value="<?php echo $config['pc_name']; ?>"></td>
					<td><input type="text" id="ip_address_<?php echo $config['pc_name']; ?>" value="<?php echo $config['ip_address']; ?>" disabled></td>
					<td><button onclick="editConfig('ip_address_<?php echo $config['pc_name']; ?>')">Edit</button></td>				
					<td><input type="text" id="submask_<?php echo $config['pc_name']; ?>" value="<?php echo $config['submask']; ?>" disabled></td>	
					<td><button onclick="editConfig('submask_<?php echo $config['pc_name']; ?>')">Edit</button></td>	
					<td><input type="text" id="vlan_<?php echo $config['pc_name']; ?>" value="<?php echo $config['vlan']; ?>" disabled></td>
					<td><button onclick="editConfig('vlan_<?php echo $config['pc_name']; ?>')">Edit</button></td>		
					<td><input type="text" id="gateway_<?php echo $config['pc_name']; ?>" value="<?php echo $config['gateway']; ?>" disabled></td>	
					<td><button onclick="editConfig('gateway_<?php echo $config['pc_name']; ?>')">Edit</button></td>
				</tr>
				<?php endforeach; ?>
			</table>
			<!-- Button für den funktionsaufruf um die Änderungen zu speichern (db) -->
			<button onclick="saveChanges()">Save Changes</button>
		</div>
	
		<div class="nav">
			<div class="logout-button">
				<a href="logout.php" class="nav-link">Logout</a>
			</div>
		</div>
	</body>
</html>