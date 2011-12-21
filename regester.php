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
		
		$cleanEmail = $con->escape_string($emailAddress);
		
		$sql = "SELECT COUNT(*) FROM tb_users WHERE email_address = '$cleanEmail'";
		if($result = $con->query($sql))
		{
			if($row = $result->fetch_row() && (int)$row[0] > 0)
			{
				die("Email address already in use.");
			}
		}
		else
			echo "Error checking for existing emails. ";
		//$con->close();
		
		//"INSERT INTO 'projecttransparency'.'tb_user_confirm' ('id', 'email_address', 'password', 'last_name', 'first_name', 'gender', 'birth_year', 'registered', 'email_sent') VALUES (NULL, \' spencersshutter@hotmail.com\', \'c7c9cfbb7ed7d1cebb7a4442dc30877f\', \'Oberstadt\', \'Spencer\', \'M\', \'1991\', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP');";
		$sql = "INSERT INTO tb_user_confirm (email_address, password, last_name, first_name, gender, birth_year, email_sent, confCode)
		 VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, ?)";
		//echo '$stmt: ' . $stmt;
		if(!($stmt = $con->prepare($sql)))
			echo $con->error;
		$stmt->bind_param('sssssis', $emailAddress, $password, $lastName, $firstName, $gender, $birthYear, $confCode);
		if($stmt->execute())
		{
			$firstName = $_REQUEST['firstName'];
			if(mail($_REQUEST['emailAddress'], "Confirm Project Transparency Account", 
				"Welcome to Project Transparency, $firstName. " .
				"We are excited for you to see everything that this project can help you with.

Copy the following link into your browser to activate your account: http://" . $_SERVER['SERVER_NAME'] . "/projectTransparency/project/activate.php?c=$confCode", 'From: no-response-project@256design.com'))
				echo ("Success");
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