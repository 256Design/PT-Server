<?php
	require 'dbConnect.php';
	
	$userID;
	
	if(isset($_GET['id']))
		$userID = (int)$_GET['id'];
	else
	{
		header("Status: 412 Precondition Failed");
		die("Error getting user id");
	}
	
	$con = makeSQLI();
	if($con === false)
	{
		header("Status: 412 Precondition Failed");
		die("Error connecting to sql");
	}
	
	$count = 0;
	
	if(isset($_POST['questions']) && $_POST['questions'] != "")
	{
		$newUpdateQuestionsStringArray = explode("\n", $_POST['questions']);
		foreach ($newUpdateQuestionsStringArray as $newQuestionString)
		{
			if($newQuestionString != "")
			{
				list($qID, $question, $type, $positive) = explode("|", $newQuestionString);
				if($qID == -1)
				{
					// insert new
					$sql = "INSERT INTO tb_user_questions (`fk_user_id`, `question` ,`type` ,`positive`)
					 VALUES (?, ?, ?, ?)";
					if(!($stmt = $con->prepare($sql)))
					{
						header("Status: 412 Precondition Failed");
						die("Error prep'ing new question");
					}
					$stmt->bind_param('isss', $userID, $question, $type, $positive);
					if(!$stmt->execute())
					{
						header("Status: 412 Precondition Failed");
						die("Error adding question");
					}
				}
				else
				{
					// update on row num
					$cleanQ = $con->escape_string($question);
					$sql = "UPDATE tb_user_questions SET question = '". $cleanQ ."'
							, type = '".$type."', positive = '". $positive ."'
							WHERE fk_user_id = " . $userID .
							" AND id = " . $qID;
					if(!$result = $con->query($sql))
					{
						header("Status: 412 Precondition Failed");
						die("Error updating question");
					}
				}
			}
		}
	}
	
	$cleanID = $con->escape_string($userID);
	$sql = "SELECT * FROM tb_user_questions WHERE fk_user_id = '$cleanID'";
	if($result = $con->query($sql))
	{
		$toPrint = "";
		while ($row = $result->fetch_object()) {
			$toPrint .= $row->id."|".$row->question."|".$row->type."|".$row->positive."|".$row->added."\n";
			$count++;
		}
	}
	if($count == 0)
	{
		header("Status: 204 No Content");
		echo "None";
	}
	else
	{
		header("Status: 202 Accepted");
		echo $toPrint;
	}
?>