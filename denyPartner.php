<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>256 Design - Spencer Oberstadt - Web Developer in Stevens Point, WI</title>
</head>
<body>
<?php
	if(isset($_GET['c']))
		$c = $_GET['c'];
	else
		die("Stop trying to cheat.");
	
	require 'dbConnect.php';
	
	$con = makeSQLI();
	if($con === false)
		die("ERROR CONNECTING TO DATABASE. Sorry...");
	
	$cleanC = $con->escape_string($c);
	
	$sql = "SELECT * FROM tb_partner_confirm WHERE confCode = '$cleanC'";
	if($result = $con->query($sql))
	{
		$row = $result->fetch_object();
		$email = $row->partner_email_address;
		$userID = $row->fk_user_id;
		$sql = "INSERT INTO tb_partner_reject (fk_user_id, partner_email_address)
		 VALUES ('$userID', '$email')";
		if($result = $con->query($sql))
		{
			if($con->affected_rows == 1)
			{
				$sql = "DELETE from tb_partner_confirm WHERE confCode = '$cleanC'";
				$con->query($sql);
				echo "Rejection saved. This person with not be able be able to add you any more.";
			}
			else
				echo "Error regestering. Maybe try again? Or email pt@256design.com to inform us of what happened.";
		}
		else echo "Something when wrong. Good thing there is now no condemnation for us who are in Christ Jesus. " + $con->error;
	}
	else echo "Something when wrong. Good thing there is now no condemnation for us who are in Christ Jesus.";
?>
</body>
</html>