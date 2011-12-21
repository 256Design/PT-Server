<?php
	require 'dbConnect.php';
	require 'validation.php';

	$emailAddress;
	$userID;
	
	if (isset($_REQUEST['emailAddress']) &&
		isset($_REQUEST['userID']) && 
		validate())
	{
		$con = makeSQLI();
		if($con === false)
			die('Error ' . mysqli_connect_error);
		
		$emailAddress = ($_REQUEST['emailAddress']);
		$userID = ($_REQUEST['userID']);
		
		$confCode = md5($emailAddress . rand(1, 99));
		
		$cleanEmail = $con->escape_string($emailAddress);
		$cleanID = $con->escape_string($userID);
		
		$sql = "SELECT COUNT(*) FROM tb_partner_confirm c, tb_partner_relation r 
				WHERE (c.fk_user_id = '$cleanID' AND c.partner_email_address = '$cleanEmail')
				 OR (r.fk_user_id = '$cleanID' AND r.partner_email_address = '$cleanEmail')";
		if($result = $con->query($sql))
		{
			if($row = $result->fetch_row() && (int)$row[0] > 0)
				die("Partner relation already exists.");
		}
		else
			die("Error checking for existing emails.");
		//$con->close();
		
		$sql = "INSERT INTO tb_partner_confirm (fk_user_id, partner_email_address, email_sent, confCode)
		 VALUES (?, ?, CURRENT_TIMESTAMP, ?)";
		//echo '$stmt: ' . $stmt;
		if(!($stmt = $con->prepare($sql)))
			echo $con->error;
		$stmt->bind_param('iss', $userID, $emailAddress, $confCode);
		if($stmt->execute())
		{
			$sql = "SELECT email_address, first_name, last_name FROM tb_users WHERE id = '$cleanID'";
			if($result = $con->query($sql))
			{
				if($result->num_rows == 1)
				{
					$row = $result->fetch_row();
					
					$userEmail = $row[0];
					$userFirstName = $row[1];
					$userLastName = $row[2];
					$firstName = $_REQUEST['firstName'];
					if(mail($_REQUEST['emailAddress'], "Confirm Project Transparency Partnership", 
						"Welcome to Project Transparency. " .
						"You are receiving this because $userFirstName $userLastName($userEmail) claimed you as their accountablity partner. 
Before subscribing you to their daily updates, we would like to confirm with you first that you actually know this person.
	
To confirm that you know this person and would like daily accountablity updates, click the following link or copy it into your browser: 
http://" . $_SERVER['SERVER_NAME'] . "/projectTransparency/project/activatePartner.php?c=$confCode

Don't know this person or you don't want to recieve their email updates? Click the following link or copy it into your browser to block them:
http://" . $_SERVER['SERVER_NAME'] . "/projectTransparency/project/denyPartner.php?c=$confCode",
					'From: no-response-project@256design.com'))
						echo ("Success");
					else
						die('Error sending email');
				}
				else
					die('Error sending email');
			}
			else
				die('Error adding partner.');
		}
		else
			die('Error adding partner.');
	}
	else
	{
		if(isset($_REQUEST['userID']))
			echo "Invalid Email.";
		else
			echo "Invalid UserID.";
	}
	
	function validate()
	{
		$email = $_REQUEST['emailAddress'];
		$userID = $_REQUEST['userID'];
		
		return validateEmail($email);
	}
?>