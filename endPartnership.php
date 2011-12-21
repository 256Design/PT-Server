<?php
	require 'dbConnect.php';
	
	if (isset($_REQUEST['id']))
	{
		$con = makeSQLI();
		if($con === false)
			die('Error ' . mysqli_connect_error);
		
		$realID = (int)substr(trim($_REQUEST['id']), 2, -3);
		
		$sql = "SELECT * ".
		"FROM tb_partner_relation, tb_users ".
		"WHERE tb_partner_relation.id = " . $realID . " AND fk_user_id = tb_users.id";
		$row;
		if($result = $con->query($sql))
		{
			if(!$row = $result->fetch_array())
				die("Non-existant partnership: $realID");
		}
		else {
			die("Error getting partnership information: " . $con->error);
		}
		
		$sql = "DELETE FROM tb_partner_relation WHERE id = " . $realID;
		if($result = $con->query($sql) && mysqli_affected_rows($con) == 1)
		{		
			$subject = "Project Transparency - Partnership Change";
			$message = "We are sorry to inform you that your partnership with " . $row['partner_email_address'] . " per their request. If this was not expected, I would suggest talking to them.";
			$someSent = false;
			$to = $row['email_address'];
			if(mail($to, $subject, $message, 'From: no-response-project@256design.com'))
			{
				die("Successfully cancelled partnership with ".$to." and they have recieved an email with the following message:</br>".$message);
			}
			else
			{
				die("Unsuccessfully sent email. Please make sure your ex-partner is informed.");
			}
		}
		else
		{
			die("Could not delete partnership. Error: " . $con->error);
		}
	}
	else
	{
		die("Error with inputted data.");
	}
?>