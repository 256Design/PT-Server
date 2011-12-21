<?php
require 'dbConnect.php';
require 'validation.php';


if(isset($_GET['fake']))
{
	sleep(3);
	header("Status: 202 Accepted");
	die("");
}


$emailAddress;
$userID;

if (!isset($_GET['userID']))
{
	header("Status: 412 Precondition Failed");
	die("Error: No user id");
}
$userID = $_GET['userID'];

$con = makeSQLI();
if($con === false)
{
	header("Status: 412 Precondition Failed");
	die("Error connecting to db");
}

$count = 0;
if(isset($_POST['responses']))
{
	$responses = $_POST['responses'];
	$rawResponsesArray = explode("\n", $responses);
	//$rawResponsesArray = array(1,2);
	
	$partnersArray = array();
	$responsesArray = array();
	$questionsArray = array();
	
	if(count($rawResponsesArray) >= 1)
	{
		$cleanID = $con->escape_string($userID);
		$sql = "SELECT * FROM tb_users WHERE id = $cleanID";
		if(!$result = $con->query($sql))
		{
			header("Status: 412 Precondition Failed");
			die("Error getting users.");
		}
		if(!$userRow = $result->fetch_array())
		{
			header("Status: 412 Precondition Failed");
			die("Error: user id invalid.");
		}
		
		$sql = "SELECT * FROM tb_partner_relation WHERE fk_user_id = $cleanID";
		if(!$result = $con->query($sql))
		{
			header("Status: 412 Precondition Failed");
			die("Error getting partners.");
		}
		echo "emails: ". $result->num_rows ."\n";
		while ($row = $result->fetch_array())
		{
			$email = "" . $row['partner_email_address'];
			$partnersArray[$row['id']] = $email;
			echo "email: " . $email . "\n";
		}
		
		foreach ($rawResponsesArray as $rawResponse)
		{
			if($rawResponse != "")
			{
				list($qID, $response) = explode("|", $rawResponse);
				$responsesArray[$qID] = $response;
			}
		}
	
		$sql = "SELECT * FROM tb_user_questions WHERE fk_user_id = $cleanID";
		if(!$result = $con->query($sql))
		{
			header("Status: 412 Precondition Failed");
			die("Error getting partners.");
		}
		while ($row = $result->fetch_array())
		{
			array_push($questionsArray, $row);
		}
		
		$to = 'soberstadt@gmail.com';
		$subject = "Project Transparency - " . $userRow['first_name'] . "'s Daily Report";
		$message = "Daily Accountability Report\r\n" .
			"for " . $userRow['first_name'] . "\r\n\r\nHere are their responses:\r\n";
		for ($i = 0; $i < count($questionsArray); $i++) {
			$message .= ($i+1).". " . $questionsArray[$i]['question'] . "   Response: "
				. $responsesArray[$questionsArray[$i]['id']] . "\r\n";
		}
		$someSent = false;
		foreach ($partnersArray as $key => $partner) {
			$to = $partner;
			$sendMessage = $message . "\r\nIf you would like to stop receiving these email, got to http://" . 
					$_SERVER['SERVER_NAME'] . "/projectTransparency/project/endPartnership.php?id=02".$key."7".rand(10, 99)." (NOTICE: They will be notified).";
			//echo $sendMessage."<br/>";
			if(mail($to, $subject, $sendMessage, 'From: no-response-project@256design.com') && !$someSent)
			{
				$someSent = true;
			}
		}
		if(isset($_GET['includeSelf']))
		{
			$to= $userRow['email_address'];
			$subject = "Project Transparency - Your Daily Report";
			mail($to, $subject, $message, 'From: no-response-project@256design.com');
		}
		if($someSent)
		{
			header("Status: 202 Accepted");
		}
		else
			header("Status: 409 Conflict");
			
	}
}
else
{
	header("Status: 412 Precondition Failed");
	die("Error: no responses sent");
}