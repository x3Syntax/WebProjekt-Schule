<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: content-type");

include './db.php';

session_start();

$sID = $_POST['username'];
$sPW = $_POST['password'];

$dbSession = new DBClass();
$dbSession->connect_db();

if ($dbSession->check_login_Data($sID,$sPW) != 0)
{
	$_SESSION['loggedin'] = true;
	$dbSession->clearSession();
}
else
{
	echo 'Incorrect username and/or password!';
	$dbSession->clearSession();
}


if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) 
{
	$dbSession->clearSession();
    header('Location: dashboard.php');
    exit;
} 
?>