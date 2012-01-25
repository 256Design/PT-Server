<?php
	require 'dbConnect.php';
	require 'validation.php';

	$emailAddress;
	$userID;
	
	if (isset($_REQUEST['e']))
	{
		$con = makeSQLI();
		if($con === false)
			errorOut('Error ' . mysqli_connect_error);
		
		$emailAddress = ($_REQUEST['e']);
		$cleanEmail = $con->escape_string($emailAddress);
		
		$confCode = md5($emailAddress . rand(1, 99));
		
		$sql = "SELECT id FROM tb_users WHERE email_address = '$cleanEmail'";
		if($result = $con->query($sql))
		{
			if(!($row = $result->fetch_row()) || $result->num_rows != 1)
			{
				errorOut("Email not used.");
			}
			else
				$userID = $row[0];
		}
		else
			errorOut("Error getting id from email.");
		
		$sql = "INSERT INTO tb_password_recover (user_id, conf_code)
				 VALUES (?, ?)";
		//echo '$stmt: ' . $stmt;
		if(!($stmt = $con->prepare($sql)))
		echo $con->error;
		$stmt->bind_param('is', $userID, $confCode);
		if($stmt->execute())
		{
			$subject = "Project Transparency Account Recovery";
			$message = "Forgot your password? Don't worry, I have just the solution. " .
					   "Hit or copy the following link to enter a new password.\r\n\r\n" .
					   "http://" . $_SERVER['SERVER_NAME'] . "/projectTransparency/project/changePassword.php?c=$confCode";
			$header = 'From: no-response-project@256design.com';
			if(mail($emailAddress, $subject, $message, $header))
			
			{
				header("Status: 202 Accepted");
				echo ("Success");
			}
			else
				errorOut('Error sending email');
		}
		else
			die('Error adding account, you may already be in the system somehow, try going back and trying to log in again.');
	}
	else 
	{
		errorOut("No email");
	}
	
	function errorOut($text)
	{
		header("Status: 401 Unauthorized");
		die($text);
	}
?>