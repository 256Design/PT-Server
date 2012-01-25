<?php
	require 'dbConnect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Project Transparency - Change Password</title>
</head>

<body>
	<?php 
	$con;
	
	function checkCode($confCode)
	{
		$con = makeCon();
		$sql = "SELECT user_id FROM tb_password_recover WHERE conf_code ='" .
				$con->escape_string($confCode) . "'";
		if($result = $con->query($sql))
		{
			$row = $result->fetch_row();
			if((int)$result->num_rows == 1)
			{
				return $row[0];
			}
			else
				return false;
		}
		else
			echo $con->error;
	}
	
	function makeCon()
	{
		if(!isset($con) || $con == null)
		{
			$con = makeSQLI();
			if($con === false)
			die('Error ' . mysqli_connect_error);
		}
		return $con;
	}
	
	$pass = false;
	if(((isset($_POST['emailTB']) && isset($_POST['oldPassTB'])) || isset($_GET['c'])) && 
		isset($_POST['passwordTB']) && 
		isset($_POST['passwordConfTB']))
	{
		if(isset($_POST['emailTB']) && strlen($_POST['emailTB']) == 0)
			$error = "You must enter an email address";
		else if($_POST['passwordTB'] != $_POST['passwordConfTB'])
			$error = "Passwords did not match";
		else if(strlen($_POST['passwordTB'])<5)
			$error = "Password must be at least 5 characters";
		// Valid fields
		else
		{
			if(isset($_POST['emailTB']))
			{
				$con = makeCon();
					
				$cleanEmail = $con->escape_string($_POST['emailTB']);
				$existingPass = md5($_POST['oldPassTB']);
				$newPass = (md5($_POST['passwordTB']));
				
				$sql = "UPDATE tb_users " . 
					   "SET password = '$newPass' " .
					   "WHERE email_address = '$cleanEmail' " . 
					   "AND password = '$existingPass'";
				if($result = $con->query($sql))
				{
					if($con->affected_rows == 1)
					{
						$pass = true;
					}
					else 
					{
						$error = "Invalid Email/Password Combo.";
					}
				}
				else
				{
					$error = "Error updating user password.";
				}
			}
			else
			{
				$con = makeCon();
					
				$userID = checkCode((string)$_GET['c']);
				$newPass = md5($_POST['passwordTB']);
				if($userID)
				{
					$sql = "UPDATE tb_users " . 
						   "SET password = '$newPass' " .
						   "WHERE id = $userID";
					if(!$result = $con->query($sql))
					{
						$error = "Error updating user password.";
					}
					else
					{
						$sql = "DELETE FROM tb_password_recover " .
						             "WHERE conf_code = '" . $_GET['c'] ."'";
						$pass = $con->query($sql);
					}
				}
				else
					$error = "Invalid confirmation code.";
			}
		}
	}
	if(!$pass)
	{
	?>
	<h1>Change Your Password</h1>
	<?php
	if(isset($error))
		echo "<h3 style='color: red;'>$error</h3>\n";
	?>
	<form method="post">
	<?php if(!isset($_GET['c']))
	{
	?>
		<label for="emailTB">Email Address:</label>
		<input type="email" name="emailTB" id="emailTB" value="<?php 
		if(isset($_POST['emailTB'])) echo $_POST['emailTB']; 
		?>"/><br />
		<label for="oldPassTB">Current Password:</label>
		<input type="password" name="oldPassTB" id="oldPassTB"/><br />
	<?php } ?>
	<label for="passwordTB">Password:</label>
	<input type="password" name="passwordTB" id="passwordTB" /><br />
	<label for="passwordConfTB">Confirm Password:</label>
	<input type="password" name="passwordConfTB" id="passwordConfTB" /><br />
	<input type="submit" value="Submit"/>
	<?php 
	}
	else 
		echo "Successfully changed password."
	?>
	</form>
</body>

</html>