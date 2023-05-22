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
	location.reload();
}

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
	//xhr.onreadystatechange = function() {
	//	if (xhr.readyState === 4 && xhr.status === 200) {
	//		alert(xhr.responseText);
	//	}
	//};
	///////////////////
	xhr.open('POST', 'users.php');
	xhr.send(data);
	
	alert("Changes applied");
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

	this.postData(form_data);
	
	location.reload();
}