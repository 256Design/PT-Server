<?php
	require 'dbConnect.php';
	require 'validation.php';
	
	$emailAddress;
	$userID;
	
	if (!isset($_REQUEST['userID']))
	{
		header("Status: 412 Precondition Failed");
		die("Error");
	}
	$userID = $_REQUEST['userID'];
	
	$con = makeSQLI();
	if($con === false)
	{
		header("Status: 412 Precondition Failed");
		die("Error");
	}
	
	if (isset($_GET['add']))
	{
		if(isset($_REQUEST['question'])
			&& isset($_REQUEST['type'])
			&& isset($_REQUEST['positive']))
		{
			$question = $_REQUEST['question'];
			$type = $_REQUEST['type'];
			$positive = $_REQUEST['positive'];
			$sql = "INSERT INTO tb_user_questions (`fk_user_id`, `question` ,`type` ,`positive`)
				 VALUES (?, ?, ?, ?)";
			if(!($stmt = $con->prepare($sql)))
			{
				header("Status: 412 Precondition Failed");
				die("Error: " . $con->error);
			}
			$stmt->bind_param('isss', $userID, $question, $type, $positive);
			if(!$stmt->execute())
			{
				header('', true, 409);
				die("Error dup: " . $con->error);
			}
			//header('', true, 201);
			header("Status: 201 Created");
			echo $con->insert_id;
		}
		else 
		{
			header("Status: 412 Precondition Failed");
			die("Error with data.");
		}
	}
	else if (isset($_GET['update']))
	{
		if (isset($_REQUEST['questionID'])
			&& isset($_REQUEST['question'])
			&& isset($_REQUEST['type'])
			&& isset($_REQUEST['positive']))
		{
			$qID = $_REQUEST['questionID']; 
			$cleanQ = $con->escape_string($_REQUEST['question']);
			$type = $_REQUEST['type'];
			$positive = $_REQUEST['positive'];
			$sql = "UPDATE tb_user_questions SET question = '". $cleanQ ."'
						, type = '".$type."', positive = '". $positive ."'
						WHERE fk_user_id = " . $userID .
						" AND id = " . $qID;
			if(!$result = $con->query($sql))
			{
				header("Status: 412 Precondition Failed");
				die("Error: " . $con->error);
			}
			header("Status: 202 Accepted");
			//echo $con->insert_id;
		}
		else
		{
			header("Status: 412 Precondition Failed");
			die("Error with data.");
		}
	}
	else if (isset($_GET['delete']))
	{
		if (isset($_REQUEST['questionID'])
			&& isset($_REQUEST['question'])
			&& isset($_REQUEST['type'])
			&& isset($_REQUEST['positive']))
		{
			$qID = $_REQUEST['questionID']; 
			$cleanQ = $con->escape_string($_REQUEST['question']);
			$type = $_REQUEST['type'];
			$positive = $_REQUEST['positive'];
			$sql = "DELETE FROM tb_user_questions WHERE question = '". $cleanQ .
						"' AND type = '".$type."' AND positive = '". $positive .
						"' AND fk_user_id = " . $userID . 
						" AND id = " . $qID;
			if(!$result = $con->query($sql))
			{
				header("Status: 412 Precondition Failed");
				die("Error: " . $con->error);
			}
			header("Status: 202 Accepted");
			//echo $con->insert_id;
		}
		else
		{
			header("Status: 412 Precondition Failed");
			die("Error with data.");
		}
	}
?>