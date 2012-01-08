<?php
	require 'dbConnect.php';

$emailAddress;
$password;

if (isset($_REQUEST['emailAddress']) &&
	isset($_REQUEST['password']))
{
	$con = makeSQLI();
	if($con === false) die('Error ' . mysqli_connect_error);

	$emailAddress = ($_REQUEST['emailAddress']);
	$password = (md5($_REQUEST['password']));

	$cleanEmail = $con->escape_string($emailAddress);

	$sql = "SELECT id FROM tb_users WHERE email_address = '$cleanEmail' AND password = '$password'";
	if($result = $con->query($sql))
	{
		$row = $result->fetch_row();
		if($result->num_rows == 1)
		{
			$sql = "UPDATE tb_users " .
			          "SET last_login = NOW() " .
			          "WHERE email_address = '$cleanEmail'";
			$con->query($sql);
			
			header("Status: 202 Accepted");
			die($row[0]);			
		}
	}
	else
	{
		header("Status: 401 Unauthorized");
		echo "Error logging in.";
	}
}
else
{
	header("Status: 401 Unauthorized");
	echo "Invalid Data.";
}
?>