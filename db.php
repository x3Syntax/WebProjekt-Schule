<?php
	class DBClass
	{
		private $pdo;
		
		public function connect_db()
		{
			try 
			{
				$this->pdo = new PDO('mysql:host=localhost; dbname=schulprojekt', 'root', 'admin');
			} 
			catch (PDOException $e) 
			{
				die("Could not connect to the database $dbname :" . $e->getMessage());
			}
			
			$this->pdo->query("set names 'utf8';");
		}
		
		public function check_login_Data($loginID, $loginPW) 
		{		
			$sql = "SELECT aID FROM account WHERE login_ID = ".$this->pdo->quote($loginID)." AND login_PW = ".$this->pdo->quote($loginPW)."";
		
			$getAccountInfo = $this->pdo->prepare($sql);
			$getAccountInfo->execute();
			$dbAccInfo = $getAccountInfo->fetchAll();
			
			unset($getAccountInfo);
			
			if ($dbAccInfo) 
			{
				return $dbAccInfo[0]['aID'];
			} 
			else 
			{
				//echo 'Error: No dbAccInfo.';
				return 0;
			}
			
			return 0;
		}
		
		public function getAllConfigs()
		{
			$sql = "SELECT * FROM devices WHERE 1 = 1";
			
			$getConfigs = $this->pdo->prepare($sql);
			$getConfigs->execute();
			$orderInfo = $getConfigs->fetchAll();
			
			return $orderInfo;
		}
		
		public function insertChangesPC($configs)
		{
			//SQL statement
			$updateConfigs = $this->pdo->prepare('UPDATE devices SET ip_address = ?, submask = ?, vlan = ?, gateway = ? WHERE pc_name = ?');
			
			//Iterieren durch das array um für jedes objekt das statement auszuführen
			foreach ($configs as $config) 
			{
				$params = [$config['ip_address'], $config['submask'], $config['vlan'], $config['gateway'], $config['pc_name']];
				$updateConfigs->execute($params);
				
				//Debug output
				//$updateConfigs->debugDumpParams();
			}
		}
		
		public function getAllAccs()
		{
			$sql = "SELECT account.aID, account.login_ID, account.login_PW, users.vorname FROM account, users WHERE account.aID = users.uID";
			
			$getConfigs = $this->pdo->prepare($sql);
			$getConfigs->execute();
			$orderInfo = $getConfigs->fetchAll();
			
			return $orderInfo;
		}
		
		public function insertChangesAccs($accounts)
		{
			//SQL statement
			$updateAccounts = $this->pdo->prepare('UPDATE account SET login_ID = ?, login_PW = ? WHERE aID = ?');
			
			//Iterieren durch das array um für jedes objekt das statement auszuführen
			foreach ($accounts as $account) 
			{	
				$params = [$account['login_ID'], hash('sha256',$account['login_PW']), $account['aID']];
				$updateAccounts->execute($params);
				
				//Debug output
				//$updateConfigs->debugDumpParams();
			}
		}
		
		public function removeAcc($acc)
		{
			//SQL statement
			$sql = "DELETE FROM account WHERE aID = ".$this->pdo->quote($acc).";
					DELETE FROM users WHERE uID = ".$this->pdo->quote($acc)."
					";
			
			$getSql = $this->pdo->prepare($sql);
			$getSql->execute();
			$getSql->fetchAll();
		}
		
		public function addAcc($accs)
		{
			
			foreach ($accs as $acc) 
			{
				$sql = "INSERT INTO account (login_ID, login_PW) VALUES (
					".$this->pdo->quote($acc['login_ID']).",
					".$this->pdo->quote(hash('sha256',$acc['login_PW']))."); 
					
					INSERT INTO users (name, vorname) VALUES (
					".$this->pdo->quote($acc['name2']).",
					".$this->pdo->quote($acc['name'])."); 
					
					" ;
			}
			
			$getSql = $this->pdo->prepare($sql);
			$getSql->execute();
			$getSql->fetchAll();
		}
		
		public function exportCSV()
		{
			//Execute a SELECT statement to fetch the data
			$sql = $this->pdo->query('SELECT * FROM devices');

			// Open a file handle for writing the CSV file
			$fp = fopen('exported.csv', 'w');

			// Loop through the result set and write each row to the CSV file
			while ($row = $sql->fetch(PDO::FETCH_ASSOC)) 
			{
				fputcsv($fp, $row);
			}

			// Close the file handle and disconnect from the database
			fclose($fp);
		}
		
		public function exportXML()
		{
			// Execute a SELECT statement to fetch the data
			$sql = $this->pdo->query('SELECT * FROM devices');

			// Create a new XML document
			$xmlDoc = new DOMDocument('1.0', 'UTF-8');

			// Create the root element
			$root = $xmlDoc->createElement('devices');
			$xmlDoc->appendChild($root);

			// Loop through the result set and create XML elements for each row
			while ($row = $sql->fetch(PDO::FETCH_ASSOC)) 
			{
				// Create an element for each row
				$device = $xmlDoc->createElement('device');

				// Loop through the row data and create child elements
				foreach ($row as $columnName => $value) 
				{
					$element = $xmlDoc->createElement($columnName, $value);
					$device->appendChild($element);
				}
				// Append the device element to the root
				$root->appendChild($device);
			}
			// Format the XML output
			$xmlDoc->formatOutput = true;
			// Save the XML to a file
			$xmlDoc->save('exported.xml');
		}
		
		public function clearSession()
		{
			unset($pdo);
		}
		
		public function importCSVdata($getData)
		{
			[$pc_name, $ip_address, $submask, $vlan, $gateway] = $getData;
			
			
			$cacheFile = 'cache.txt';
			
			 // Check if the cache file exists and is not expired
			if (file_exists($cacheFile)) 
			{
				// Cache file exists and is not expired, retrieve the data from the cache file
				$data = unserialize(file_get_contents($cacheFile));

				// Cache file does not exist or is expired, retrieve the data from the database
				$sql = $this->pdo->prepare("SELECT pc_name FROM devices WHERE pc_name = ".$this->pdo->quote($pc_name)."");
				$sql->execute();
				
				if ($sql->rowCount() > 0) 
				{
					echo ' 
						<div class="w3-main" style="margin-left:20%; margin-top: 3%;">
							<div class="w3-main" style="margin-top: 0%;">
								<table class="w3-table">
									<tr>
										<th class="w3-red">- EDIT -</th>
										<th>IP Address</th>
										<th>Submask</th>
										<th>VLAN</th>
										<th>Gateway</th>
									</tr>
									<tr>
										<td>'.$pc_name.'<input type="hidden" name="pc_name" value="'.$pc_name.'"></td>
										<td><input class="w3-input" type="text" id="ip_address_'.$pc_name.'" value="'.$ip_address.'" disabled></td>				
										<td><input class="w3-input" type="text" id="submask_'.$pc_name.'" value="'.$submask.'" disabled></td>	
										<td><input class="w3-input" type="text" id="vlan_'.$pc_name.'" value="'.$vlan.'" disabled></td>		
										<td><input class="w3-input" type="text" id="gateway_'.$pc_name.'" value="'.$gateway.'" disabled></td>	
									</tr>
								</table>
							</div>
						</div>
						'; 
					
					$data = [
						'status' => 'edit',
						'pc_name' => $pc_name,
						'ip_address' => $ip_address,
						'submask' => $submask,
						'vlan' => $vlan,
						'gateway' => $gateway
					];
				} 
				else 
				{
					echo ' 
				<div class="w3-main" style="margin-left:20%; margin-top: 3%;">
					<div class="w3-main" style="margin-top: 0%;">
						<table class="w3-table">
							<tr>
								<th class="w3-green">IMPORT</th>
								<th>IP Address</th>
								<th>Submask</th>
								<th>VLAN</th>
								<th>Gateway</th>
							</tr>
							<tr>
								<td>'.$pc_name.'<input type="hidden" name="pc_name" value="'.$pc_name.'"></td>
								<td><input class="w3-input" type="text" id="ip_address_'.$pc_name.'" value="'.$ip_address.'" disabled></td>				
								<td><input class="w3-input" type="text" id="submask_'.$pc_name.'" value="'.$submask.'" disabled></td>	
								<td><input class="w3-input" type="text" id="vlan_'.$pc_name.'" value="'.$vlan.'" disabled></td>		
								<td><input class="w3-input" type="text" id="gateway_'.$pc_name.'" value="'.$gateway.'" disabled></td>	
							</tr>
						</table>
					</div>
				</div>
				'; 
					
					$data = [
						'status' => 'import',
						'pc_name' => $pc_name,
						'ip_address' => $ip_address,
						'submask' => $submask,
						'vlan' => $vlan,
						'gateway' => $gateway
					];
				}
  
			// Store the data in the cache file
			file_put_contents($cacheFile, serialize($data) . PHP_EOL, FILE_APPEND);
			}
		}	
		
		public function loadCache($cacheFile)
		{
			$cachedData = [];
			
			if (file_exists($cacheFile)) 
			{
				$fileContent = file_get_contents($cacheFile);
				$lines = explode(PHP_EOL, $fileContent);
				
				foreach ($lines as $line) 
				{
					$data = unserialize($line);
					$cachedData[] = $data;
				}
			}
			
			return $cachedData;
		}
		
		public function importApply()
		{
			$cacheFile = 'cache.txt';
			$cachedData = $this->loadCache($cacheFile);

			foreach ($cachedData as $data) 
			{
				[$pc_name, $ip_address, $submask, $vlan, $gateway] = $data;
				// Process the individual values for each cache entry
				$sql = $this->pdo->prepare("SELECT pc_name FROM devices WHERE pc_name = ".$this->pdo->quote($pc_name)."");
				$sql->execute();


				if ($sql->rowCount() > 0) 
				{				
					$sql = $this->pdo->prepare("UPDATE devices SET 
						pc_name = ".$this->pdo->quote($pc_name).", 
						ip_address = ".$this->pdo->quote($ip_address).", 
						submask = ".$this->pdo->quote($submask).",
						vlan = ".$this->pdo->quote($vlan).",
						gateway = ".$this->pdo->quote($gateway)."
					WHERE pc_name = ".$this->pdo->quote($pc_name)."");
					
					$sql->execute();
				} 
				else 
				{			
					$sql = "INSERT INTO devices (pc_name, ip_address, submask, vlan, gateway) 
						VALUES (
						".$this->pdo->quote($pc_name).", 
						".$this->pdo->quote($ip_address).", 
						".$this->pdo->quote($submask).", 
						".$this->pdo->quote($vlan).",
						".$this->pdo->quote($gateway)."
						)";
					
					$sql = $this->pdo->prepare($sql);
					$sql->execute();
				}
			}
			
			clearCache();
		}
		
		public function clearCache()
		{		
			$cacheFile = 'cache.txt';
		
			if (file_exists($cacheFile)) 
			{
				unlink($cacheFile);
			}
		}
	}
?>