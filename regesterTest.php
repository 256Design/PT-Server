<?php
	require 'dbConnect.php';
	require 'validation.php';

	$emailAddress;
	$password;
	$firstName;
	$lastName;
	$gender;
	$birthYear;
	
	if (isset($_REQUEST['emailAddress']) &&
		isset($_REQUEST['password']) && 
		isset($_REQUEST['firstName']) && 
		isset($_REQUEST['lastName']) &&
		isset($_REQUEST['gender']) &&
		isset($_REQUEST['birthYear']) &&
		validate())
	{
		$con = makeSQLI();
		if($con === false)
			die('Error ' . mysqli_connect_error);
		
		$emailAddress = ($_REQUEST['emailAddress']);
		$password = (md5($_REQUEST['password']));
		$firstName = ($_REQUEST['firstName']);
		$lastName = ($_REQUEST['lastName']);
		$gender = strtoupper(($_REQUEST['gender']));
		$birthYear = ($_REQUEST['birthYear']);
		
		$confCode = md5($emailAddress . rand(1, 50));
		
		$firstName = $_REQUEST['firstName'];
		if(mail($_REQUEST['emailAddress'], "Confirm Project Transparency Account", 
			"Welcome to Project Transparency, $firstName. " .
			"We are excited for you to see everything that this project can help you with.

Copy the following link into your browser to activate your account: http://" . $_SERVER['SERVER_NAME'] . "/projectTransperancy/project/activate.php?c=$confCode", 'From: webmaster@example.com'))
			echo ("Success");
		else
			die('Error sending email');
	}
	else
	{
		echo "Invalid Data.";
	}
	
	function validate()
	{
		$email = $_REQUEST['emailAddress'];
		$password = $_REQUEST['password'];
		$firstName = $_REQUEST['firstName'];
		$lastName = $_REQUEST['lastName'];
		$gender = $_REQUEST['gender'];
		$birthYear = $_REQUEST['birthYear'];
		
		return validateEmail($email) && validatePassowrd($password) &&
				strlen($firstName) > 0 && strlen($lastName) > 0 && 
				strlen($firstName)+strlen($lastName)>3 &&
				(strtoupper($gender) == "M" || strtoupper($gender) == "F") &&
				(int)$birthYear < date("Y") && (int)$birthYear > 1910;
	}
?>