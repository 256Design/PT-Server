 <?php
require 'dbConnect.php';
require 'validation.php';

$emailAddress;
$password;

if (isset($_REQUEST['emailAddress']) &&
	isset($_REQUEST['password']) &&
	validate())
{
	$con = makeSQLI();
	if($con === false)
	die('Error ' . mysqli_connect_error);

	$emailAddress = ($_REQUEST['emailAddress']);
	$password = (md5($_REQUEST['password']));

	$cleanEmail = $con->escape_string($emailAddress);

	$sql = "SELECT id FROM tb_users WHERE email_address = '$cleanEmail' AND password = '$password'";
	if($result = $con->query($sql))
	{
		$row = $result->fetch_row();
		if($result->num_rows == 1)
		{
			echo "Success" . $row[0];
		}
		// User does not exist in tb_users
		else
		{
			// Check if user in unconfirmed
			$sql = "SELECT COUNT(*) FROM tb_user_confirm WHERE email_address = '$cleanEmail'";
			if($result = $con->query($sql))
			{
				$row = $result->fetch_row();
				if((int)$row[0] == 1)
				{
					echo "Unconfirmed";
				}
				else
				{
					echo "Invalid";	
				}
				//echo print_r($row);
			}
			else
				echo "Error checking for existing emails. $con->error";
		}
	}
	else
		echo "Error logging in.";
}
else
{
	echo "Invalid Data.";
}

function validate()
{
$email = $_REQUEST['emailAddress'];
$password = $_REQUEST['password'];

return validateEmail($email) && validatePassowrd($password);
}
?>