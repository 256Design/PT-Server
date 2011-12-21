<?php
	require 'dbConnect.php';
	
	$userID;
	
	if(isset($_REQUEST['id']))
		$userID = (int)$_REQUEST['id'];
	else
		die("Stop trying to cheat");
	
	$con = makeSQLI();
	if($con === false)
		die("ERROR CONNECTING TO DATABASE. Sorry...");
	
	$count = 0;
	
	$cleanID = $con->escape_string($userID);
	$sql = "SELECT * FROM tb_partner_relation WHERE fk_user_id = '$cleanID'";
	if($result = $con->query($sql))
	{
		while ($row = $result->fetch_object()) {
			echo "Conf:".$row->partner_email_address."\n";
			$count++;
		}
	}
	$sql = "SELECT * FROM tb_partner_confirm WHERE fk_user_id = '$cleanID'";
	if($result = $con->query($sql))
	{
		while ($row = $result->fetch_object()) {
			echo "Unconf:".$row->partner_email_address."\n";
			$count++;
		}
	}
	if($count == 0)
		echo "None";
?>