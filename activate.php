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
	
	$sql = "SELECT * FROM tb_user_confirm WHERE confCode = '$cleanC'";
	if($result = $con->query($sql))
	{
		$row = $result->fetch_object();
		$email = $row->email_address;
		$pass = $row->password;
		$lName = $row->last_name;
		$fName = $row->first_name;
		$gender = $row->gender;
		$birthYear = $row->birth_year; 
		$sql = "INSERT INTO tb_users (email_address, password, last_name, first_name, gender, birth_year)
		 VALUES ('$email', '$pass', '$lName', '$fName','$gender','$birthYear')";
		if($result = $con->query($sql))
		{
			if($con->affected_rows == 1)
			{
				$sql = "DELETE from tb_user_confirm WHERE confCode = '$cleanC'";
				$con->query($sql);
				echo "Welcome, your account has been activated. Go ahead and log in.";
			}
			else
				echo "Your account has already been activated. Go ahead and log in.";
		}
		else echo "Something when wrong. Good thing there is now no condemnation for us who are in Christ Jesus. " + $con->error;
	}
	else echo "Something when wrong. Good thing there is now no condemnation for us who are in Christ Jesus.";
?>