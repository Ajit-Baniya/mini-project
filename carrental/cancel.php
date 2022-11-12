<?php

include('includes/config.php');
$sql = "DELETE FROM booking where id=:id";
$query = $dbh->prepare($sql);
$query->bindParam(':id', $_POST['bookingId'], PDO::PARAM_INT);
$query->execute();
echo "<script type='text/javascript'> document.location = 'my-booking.php?done'; </script>";
