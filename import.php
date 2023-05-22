<?php
include './db.php';
session_start();

if(!(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true))
{
	header('Location: index.php');
    exit;
} 


//print_r($_FILES);
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
		
			<form method="post" id="import_csv" enctype="multipart/form-data">
				<div>
					<input type="file" name="csv_file" id="csv_file" required accept=".csv" />
				</div>
				<br/>
				<button class="w3-button w3-dark-grey" type="submit" name="import_csv" class="btn btn-info" id="import_csv_btn">Load CSV</button>
			</form>
		</div>

		<?php
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{ 
				$dbSession = new DBClass();
				$dbSession->connect_db();

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

				if($_FILES['csv_file']['error'] == 0)
				{
					$ext =  $_FILES['csv_file']['type'];
					
					// check the file is a csv
					if($ext === 'text/csv')
					{
						$csvFile = fopen($_FILES['csv_file']['tmp_name'], 'r');
						
						while (($getData = fgetcsv($csvFile, 10000, ",")) !== false) 
						{
							//print_r($getData);
							$dbSession->importCSVdata($getData);
						}
						
						fclose($csvFile);
						echo '
							<div class="w3-main" style="margin-left:20%; margin-top: 3%;">
								<div class="w3-main" style="margin-top: 0%;">
									<button class="w3-button w3-dark-grey" onclick="importApply()" name="import_csv" class="btn btn-info" id="import_csv_btn">Änderungen übernehmen</button>
								</div>
							</div>
							'; 
								
						echo '<script type="text/javascript">
							window.onload = function () { alert("CSV erfolgreich importiert."); } 
						</script>';
						
					} 
					else 
					{
						echo '<script type="text/javascript">
							window.onload = function () { alert("Please select a valid file."); } 
						</script>'; 
					}	
				}
			}
		?>
	</body>
</html>

