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
			$sql = "SELECT * FROM computers WHERE 1 = 1";
			
			$getConfigs = $this->pdo->prepare($sql);
			$getConfigs->execute();
			$orderInfo = $getConfigs->fetchAll();
			
			return $orderInfo;
		}
		
		public function insertChangesPC($configs)
		{
			//SQL statement
			$updateConfigs = $this->pdo->prepare('UPDATE computers SET ip_address = ?, submask = ?, vlan = ?, gateway = ? WHERE pc_name = ?');
			
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
				$params = [$account['login_ID'], $account['login_PW'], $account['aID']];
				$updateAccounts->execute($params);
				
				//Debug output
				//$updateConfigs->debugDumpParams();
			}
		}
		
		public function removeAcc($acc)
		{
			//SQL statement
			$sql = "DELETE FROM account WHERE aID = ".$this->pdo->quote($acc).";
					DELETE FROM users WHERE oID = ".$this->pdo->quote($acc)."
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
					".$this->pdo->quote($acc['login_PW'])."); 
					
					INSERT INTO users (name, vorname) VALUES (
					".$this->pdo->quote($acc['name2']).",
					".$this->pdo->quote($acc['name'])."); 
					
					" ;
			}
			
			$getSql = $this->pdo->prepare($sql);
			$getSql->execute();
			$getSql->fetchAll();
		}
		
		public function clearSession()
		{
			unset($pdo);
		}
		
}
?>