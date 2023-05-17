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
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acc_changes'])) {
    $acc_changes = json_decode($_POST['acc_changes'], true);
    $dbSession->insertChangesAccs($acc_changes);
}

//Listener der die db funktion triggered (remove)
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acc_remove'])) {
    $acc_remove = json_decode($_POST['acc_remove'], true);
    $dbSession->removeAcc($acc_remove);
}

//Listener der die db funktion triggered (add)
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acc_add'])) {
    $acc_add = json_decode($_POST['acc_add'], true);
    $dbSession->addAcc($acc_add);
}

//Alle Infos der PC´s laden aus der db
$accs = $dbSession->getAllAccs();
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Dashboard</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<script>
		
			//Entferne Acc
			function removeAcc(acc_name)
			{
				let acc_remove = JSON.stringify(acc_name);
				
				// Erstellen eines neuen FormData-Objekt und hinzufügen des JSON-Strings als "acc_remove"
				let form_data = new FormData();
				form_data.append('acc_remove', acc_remove);
				
				this.postData(form_data);
				//Seite refreshen
				location.reload();
			}
		
			function postData(data)
			{
				let xhr = new XMLHttpRequest();	
				
				///////////DEBUG
				xhr.onreadystatechange = function() {
					if (xhr.readyState === 4 && xhr.status === 200) {
						alert(xhr.responseText);
					}
				};
				///////////////////
				xhr.open('POST', 'users.php');
				xhr.send(data);
				
				alert("Changes applied");
			}
		
		
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
				let accs = {};
	
				// Finden aller Elemente mit dem Namen "aID" und iteriere durch sie
				let aIDs = document.querySelectorAll('input[name="aID"]');
				for(let i = 0; i < aIDs.length; i++) 
				{
					//Name des PCs aus dem aktuellen Element
					let aID = aIDs[i].value;
					//Neues Objekt hinzufügen, das die aktuellen Werte der Eingabefelder für diesen PC enthält
					accs[aID] = {
						'aID': aID,
						'login_ID': document.getElementById('login_ID_' + aID).value,
						'login_PW': document.getElementById('login_PW_' + aID).value,
						'vorname': document.getElementById('vorname_' + aID).value
					};
				}
	
				// Konvertieren in einen JSON-String
				let acc_changes = JSON.stringify(accs);
				
				// Erstellen eines neuen FormData-Objekt und hinzufügen des JSON-Strings als "acc_changes"
				let form_data = new FormData();
				form_data.append('acc_changes', acc_changes);
	
				this.postData(form_data);
				location.reload();
			}
			
			function addUser()
			{
				let accTmp = {};

				accTmp[0] = {
					'login_ID': document.getElementById('login_ID').value,
					'login_PW': document.getElementById('login_PW').value,
					'name': document.getElementById('name').value,
					'name2': document.getElementById('name2').value
				};
				
				let acc_add = JSON.stringify(accTmp);
				let form_data = new FormData();
				form_data.append('acc_add', acc_add);
	
				console.log(acc_add);
	
				this.postData(form_data);
				
				location.reload();
			}
			
			
		</script>
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
			<a href="users.php" class="w3-bar-item w3-button w3-padding w3-blue"><i class="fa fa-users fa-fw"></i>  Benutzer verwalten</a>
			<a href="config.php" class="w3-bar-item w3-button w3-padding"><i class="fa fa-eye fa-fw"></i>  Netzgeräte verwalten</a>
			<a href="import.php" class="w3-bar-item w3-button w3-padding"><i class="fa fa-users fa-fw"></i>  Import Geräte</a>
			<a href="export.php" class="w3-bar-item w3-button w3-padding"><i class="fa fa-bullseye fa-fw"></i>  Export Geräte</a>
			<a href="logout.php" class="w3-bar-item w3-button w3-padding w3-red"><i class="fa fa-bullseye fa-fw"></i>  Logout</a>
		  </div>
		</nav>
		
		
		<div class="w3-main" style="margin-left:20%; margin-top: 3%;">
			<table class="w3-table">
				<tr>
					<th>aID</th>
					<th>Login ID</th>
					<th></th>
					<th>Login PW</th>
					<th></th>
					<th>Permission</th>
				</tr>
				<!-- foreach schleife um das array komplett zu Laden/anzeigen -->
				<?php foreach($accs as $acc): ?>
				<tr>
					<td><?php echo $acc['aID']; ?><input type="hidden" name="aID" value="<?php echo $acc['aID']; ?>"></td>
					<td><input class="w3-input" type="text" id="login_ID_<?php echo $acc['aID']; ?>" value="<?php echo $acc['login_ID']; ?>" disabled></td>
					<td><button class="w3-button w3-dark-grey" onclick="editConfig('login_ID_<?php echo $acc['aID']; ?>')"><img src="img/pencil.svg"></img></button></td>	
					<td><input class="w3-input" type="password" id="login_PW_<?php echo $acc['aID']; ?>" value="<?php echo $acc['login_PW']; ?>" disabled></td>	
					<td><button class="w3-button w3-dark-grey" onclick="editConfig('login_PW_<?php echo $acc['aID']; ?>')"><img src="img/pencil.svg"></img></button></td>	
					<td><input class="w3-input" type="text" id="vorname_<?php echo $acc['aID']; ?>" value="<?php echo $acc['vorname']; ?>" disabled></td>
					<td><button class="w3-button w3-red" onclick="removeAcc('<?php echo $acc['aID']; ?>')">X</button></td>		
				</tr>
				<?php endforeach; ?>
			</table>
			<!-- Button für den funktionsaufruf um die Änderungen zu speichern (db) -->
			<button class="w3-button w3-dark-grey" onclick="saveChanges()" style="margin-left: 1.1%"><img src="img/save.png"></img></button>
			
			<table class="w3-table">
				<tr>
					<th>Login ID</th>
					<th>Login PW</th>
					<th>Vorname</th>
					<th>Nachname</th>
				</tr>
				<tr>
					<td><input class="w3-input" type="text" id="login_ID"></td>
					<td><input class="w3-input" type="password" id="login_PW"></td>
					<td><input class="w3-input" type="text" id="name"></td>
					<td><input class="w3-input" type="text" id="name2"></td>	
				</tr>
			</table>
			<!-- Button für den funktionsaufruf um einen neuen User anzulegen -->
			<button class="w3-button w3-dark-grey" onclick="addUser()" style="margin-left: 1.1%"><img src="img/add.png"></img></button>
		</div>
	</body>
</html>