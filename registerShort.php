<?php
	require 'dbConnect.php';
	require 'validation.php';

	$emailAddress;
	$password;
	$firstName;
	$lastName;
	
	if (isset($_REQUEST['emailAddress']) &&
		isset($_REQUEST['password']) && 
		isset($_REQUEST['firstName']) && 
		isset($_REQUEST['lastName']) &&
		validate())
	{
		$emailAddress = $_REQUEST['emailAddress'];
		$password = $_REQUEST['password'];
		$firstName = $_REQUEST['firstName'];
		$lastName = $_REQUEST['lastName'];
		
		$con = makeSQLI();
		if($con === false)
			die('Error ' . mysqli_connect_error);
		
		$password = md5($password);
		
		$confCode = md5($emailAddress . rand(1, 50));
		
		$cleanEmail = $con->escape_string($emailAddress);
		
		$sql = "SELECT COUNT(*) FROM tb_users WHERE email_address = '$cleanEmail'";
		//Checking existing emails
		if($result = $con->query($sql))
		{
			if($row = $result->fetch_array())
			{
				if((int)$row[0] > 0)
				{
					// Email address already in use.
					header("Status: 409 Conflict");
					die("");
				}
			}
			else
				die("Error: " + $result->error());
		}
		else
			die("Error checking for existing emails.");
		//$con->close();
		//No existing entry
		
		//Adding to db
		$sql = "INSERT INTO tb_user_confirm (email_address, password, last_name, first_name, email_sent, confCode)
		 VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP, ?)";
		/*$sql = "INSERT INTO tb_user_confirm " + 
		"(email_address, password, last_name, first_name, email_sent, confCode)" +
		" VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP, ?)";*/
		//echo '$stmt: ' . $stmt;
		if(!($stmt = $con->prepare($sql)))
			die($con->error+"</br>$sql");
		//prepared
		$stmt->bind_param('sssss', 
				$emailAddress, $password, $lastName, $firstName, $confCode);
		//bound
		if($stmt->execute())
		{
			if(mail($_REQUEST['emailAddress'], "Confirm Project Transparency Account", 
				"Welcome to Project Transparency, $firstName. " .
				"We are excited for you to see everything that this project can help you with.

Copy the following link into your browser to activate your account: http://" . $_SERVER['SERVER_NAME'] . "/projectTransparency/project/activate.php?c=$confCode", 'From: no-response-project@256design.com'))
			{
				header("Status: 201 Created");
				die("Success");
			}
			else
				die('Error sending email');
		}
		else
			die('Error adding account, you may already be in the system somehow, try going back and trying to log in again.');
	}
	else
	{
		echo "Invalid Data.";
	}
	
	function validate()
	{
		$emailAddress = $_REQUEST['emailAddress'];
		$password = $_REQUEST['password'];
		$firstName = $_REQUEST['firstName'];
		$lastName = $_REQUEST['lastName'];
		
		return validateEmail($emailAddress) && validatePassowrd($password) &&
				strlen($firstName) > 0 && strlen($lastName) > 0 && 
				strlen($firstName)+strlen($lastName)>3;
	}
?>