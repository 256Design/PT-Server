<?php
$emailAddress;
$password;

// Change the following settings based on your db
// Im assuming your are using NySQL
$dbHost = "Change Me";
$dbName = "Change Me";
$dbUser = "Change Me";
$dbPassword = "Change Me";

if (isset($_REQUEST['emailAddress']) &&
	isset($_REQUEST['password']))
{
	$con = new mysqli($dbHost ,$dbPassword, $dbUser,$dbName);
	if($con === false) die('Error ' . mysqli_connect_error);

	$emailAddress = $con->escape_string($_REQUEST['emailAddress']);
	$password = (md5($_REQUEST['password']));

	$sql = "SELECT id FROM tb_users WHERE email_address = '$cleanEmail' AND password = '$password'";
	if($result = $con->query($sql))
	{
		$row = $result->fetch_row();
		if($result->num_rows == 1)
		{
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