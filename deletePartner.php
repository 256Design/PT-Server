<?php
require 'dbConnect.php';

$userID;
$email;

if(isset($_REQUEST['id']) &&
	isset($_REQUEST['email']))
{
	$userID = (int)$_REQUEST['id'];
	$email = $_REQUEST['email'];
}	
else
	die("Stop trying to cheat");

$con = makeSQLI();
if($con === false)
	die("ERROR CONNECTING TO DATABASE. Sorry...");

$count = 0;

$cleanID = $con->escape_string($userID);
$cleanEmail = $con->escape_string($email);
$sql = "DELETE from tb_partner_confirm WHERE fk_user_id = '$cleanID' AND partner_email_address = '$cleanEmail'";
if($result = $con->query($sql))
{
	die("Success");
}
$sql = "DELETE from tb_partner_relation WHERE fk_user_id = '$cleanID' AND partner_email_address = '$cleanEmail'";
if($result = $con->query($sql))
{
	echo "Success";
}
else
	echo "Failure";
?>