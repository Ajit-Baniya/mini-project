<?php 
require_once("includes/config.php");
if(isset($_GET["email"])){
	if(!empty($_POST["emailid"])) {
		$email= $_POST["emailid"];
		if (filter_var($email, FILTER_VALIDATE_EMAIL)===false) {
	
			echo "error : You did not enter a valid email.";
		}
		else {
			$sql ="SELECT EmailId FROM users WHERE EmailId=:email";
	$query= $dbh -> prepare($sql);
	$query-> bindParam(':email', $email, PDO::PARAM_STR);
	$query-> execute();
	$results = $query -> fetchAll(PDO::FETCH_OBJ);
	$cnt=1;
	if($query -> rowCount() > 0)
	{
	echo "<span style='color:red'> Email already exists .</span>";
	 echo "<script>$('#submit').prop('disabled',true);</script>";
	} else{
		
		echo "<span style='color:green'> Email available for Registration .</span>";
	 echo "<script>$('#submit').prop('disabled',false);</script>";
	}
	}
	}
} else if(isset($_GET["phone"])){
	if(!empty($_POST["mobileno"])) {
		$mobileno= $_POST["mobileno"];		
		if (!is_numeric($mobileno) && strlen($mobileno)!==10) {
			echo "error : You did not enter a valid phone number.";
			return;
		}
			$sql ="SELECT ContactNo FROM users WHERE ContactNo=:mobileno";
	$query= $dbh -> prepare($sql);
	$query-> bindParam(':mobileno', $mobileno, PDO::PARAM_STR);
	$query-> execute();
	$results = $query -> fetchAll(PDO::FETCH_OBJ);
	$cnt=1;
	if($query -> rowCount() > 0)
	{
	echo "<span style='color:red'> Phone number already exists .</span>";
	 echo "<script>$('#submit').prop('disabled',true);</script>";
	} else{
		
		echo "<span style='color:green'> Phone number available for Registration .</span>";
	 echo "<script>$('#submit').prop('disabled',false);</script>";
	}
	}
}


?>
