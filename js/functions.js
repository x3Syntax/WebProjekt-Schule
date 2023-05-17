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