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
	if($con->affected_rows == 1)
	{
		header("Status: 202 Accepted");
		//mail("spencer@256design.com", "User #".$userID." Deleted Partner: ".$email, "partner was unconfirmed.");
		die("");
	}
}
$sql = "DELETE from tb_partner_relation WHERE fk_user_id = '$cleanID' AND partner_email_address = '$cleanEmail'";
if($result = $con->query($sql))
{
	header("Status: 202 Accepted");
	//mail("spencer@256design.com", "User #".$userID." Deleted Partner: ".$email, "partner was confirmed.");
	die("");
}
else
	header("Status: 409 Conflict");
	//mail("spencer@256design.com", "User #".$userID." Failed To Deleted Partner: ".$email, "Error: ".$con->error);
	die("");
?>