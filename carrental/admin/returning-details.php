<?php
session_start();
error_reporting(0);
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
	header('location:index.php');
} else {
	if (isset($_REQUEST['eid'])) {
		$eid = intval($_GET['eid']);
		$status = "2";
		$sql = "UPDATE booking SET Status=:status WHERE  id=:eid";
		$query = $dbh->prepare($sql);
		$query->bindParam(':status', $status, PDO::PARAM_STR);
		$query->bindParam(':eid', $eid, PDO::PARAM_STR);
		$query->execute();
		echo "<script>alert('Booking Successfully Cancelled');</script>";
		echo "<script type='text/javascript'> document.location = 'canceled-bookings.php; </script>";
	}


	if (isset($_REQUEST['aeid'])) {
		$aeid = intval($_GET['aeid']);
		$status = 1;

		$sql = "UPDATE booking SET Status=:status WHERE  id=:aeid";
		$query = $dbh->prepare($sql);
		$query->bindParam(':status', $status, PDO::PARAM_STR);
		$query->bindParam(':aeid', $aeid, PDO::PARAM_STR);
		$query->execute();
		echo "<script>alert('Booking Successfully Confirmed');</script>";
		echo "<script type='text/javascript'> document.location = 'confirmed-bookings.php'; </script>";
	}

	function getDeadlineDate($bookingNumber, $dbh)
	{
		$sql = "SELECT * from booking where BookingNumber=:bookingnumber ";
		$query = $dbh->prepare($sql);
		$query->bindParam(':bookingnumber', $bookingNumber, PDO::PARAM_STR);
		$query->execute();
		$rows = $query->fetchAll(PDO::FETCH_OBJ);
		$returndate = "";
		foreach ($rows as $row) {
			$returndate = $row->ToDate;
		}

		return $returndate;
	}


	function getReturnDaysDifference($BookingNumber, $dbh)
	{
		$sql1 = "SELECT * from booking where BookingNumber=:BookingNumber";
		$query1 = $dbh->prepare($sql1);
		$query1->bindParam(':BookingNumber', $BookingNumber, PDO::PARAM_STR);
		$query1->execute();
		$result = $query1->fetch(PDO::FETCH_OBJ);

		$deadline = getDeadlineDate($BookingNumber, $dbh); // deadline date
		$todayDate = new DateTime($result->ReturnDate);
		$deadlineDate = new DateTime(date("Y-m-d", strtotime($deadline)));
		$datediff = $deadlineDate->diff($todayDate)->format('%r%a');
		if ($datediff <= 0) return 0;
		else return $datediff;
	}

?>

	<!doctype html>
	<html lang="en" class="no-js">

	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">
		<meta name="theme-color" content="#3e454c">

		<title>Car Rental Portal | New Bookings </title>

		<!-- Font awesome -->
		<link rel="stylesheet" href="css/font-awesome.min.css">
		<!-- Sandstone Bootstrap CSS -->
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<!-- Bootstrap Datatables -->
		<link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
		<!-- Bootstrap social button library -->
		<link rel="stylesheet" href="css/bootstrap-social.css">
		<!-- Bootstrap select -->
		<link rel="stylesheet" href="css/bootstrap-select.css">
		<!-- Bootstrap file input -->
		<link rel="stylesheet" href="css/fileinput.min.css">
		<!-- Awesome Bootstrap checkbox -->
		<link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
		<!-- Admin Stye -->
		<link rel="stylesheet" href="css/style.css">
		<style>
			.errorWrap {
				padding: 10px;
				margin: 0 0 20px 0;
				background: #fff;
				border-left: 4px solid #dd3d36;
				-webkit-box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
				box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
			}

			.succWrap {
				padding: 10px;
				margin: 0 0 20px 0;
				background: #fff;
				border-left: 4px solid #5cb85c;
				-webkit-box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
				box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
			}
		</style>

	</head>

	<body>
		<?php include('includes/header.php'); ?>

		<div class="ts-main-content">
			<?php include('includes/leftbar.php'); ?>
			<div class="content-wrapper">
				<div class="container-fluid">

					<div class="row">
						<div class="col-md-12">

							<h2 class="page-title">Booking Details</h2>

							<!-- Zero Configuration Table -->
							<div class="panel panel-default">
								<div class="panel-heading">Bookings Info</div>
								<div class="panel-body">


									<div id="print">
										<table border="1" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">

											<tbody>

												<?php
												$bid = intval($_GET['bid']);
												$sql = "SELECT users.*,brands.BrandName,vehicles.VehiclesTitle,booking.FromDate,booking.ToDate,booking.message,booking.VehicleId as vid,booking.Status,booking.PostingDate,booking.id,booking.BookingNumber,
DATEDIFF(booking.ToDate,booking.FromDate) as totalnodays,vehicles.PricePerDay,vehicles.FinePerDay,booking.ReturnDate
									  from booking join vehicles on vehicles.id=booking.VehicleId join users on users.EmailId=booking.userEmail join brands on vehicles.VehiclesBrand=brands.id where booking.id=:bid";
												$query = $dbh->prepare($sql);
												$query->bindParam(':bid', $bid, PDO::PARAM_STR);
												$query->execute();
												$results = $query->fetchAll(PDO::FETCH_OBJ);
												$cnt = 1;
												if ($query->rowCount() > 0) {
													foreach ($results as $result) {
														if (empty($result->ReturnDate)) {
															$returned = 0;
														} else {
															$returned = 1;
														}
												?>
														<h3 style="text-align:center; color:red">#<?php echo htmlentities($result->BookingNumber); ?> Booking Details </h3>

														<tr>
															<th colspan="4" style="text-align:center;color:blue">User Details</th>
														</tr>
														<tr>
															<th>Booking No.</th>
															<td>#<?php echo htmlentities($result->BookingNumber); ?></td>
															<th>Name</th>
															<td><?php echo htmlentities($result->FullName); ?></td>
														</tr>
														<tr>
															<th>Email Id</th>
															<td><?php echo htmlentities($result->EmailId); ?></td>
															<th>Contact No</th>
															<td><?php echo htmlentities($result->ContactNo); ?></td>
														</tr>
														<tr>
															<th>Address</th>
															<td><?php echo htmlentities($result->Address); ?></td>
															<th>City</th>
															<td><?php echo htmlentities($result->City); ?></td>
														</tr>
														<tr>
															<th>Country</th>
															<td colspan="3"><?php echo htmlentities($result->Country); ?></td>
														</tr>

														<tr>
															<th colspan="4" style="text-align:center;color:blue">Booking Details</th>
														</tr>
														<tr>
															<th>Vehicle Name</th>
															<td><a href="edit-vehicle.php?id=<?php echo htmlentities($result->vid); ?>"><?php echo htmlentities($result->BrandName); ?> , <?php echo htmlentities($result->VehiclesTitle); ?></td>
															<th>Booking Date</th>
															<td><?php echo htmlentities($result->PostingDate); ?></td>
														</tr>
														<tr>
															<th>From Date</th>
															<td><?php echo htmlentities($result->FromDate); ?></td>
															<th>To Date</th>
															<td><?php echo htmlentities($result->ToDate); ?></td>
														</tr>
														<tr>
															<th>Total Days</th>
															<td><?php echo htmlentities($tdays = $result->totalnodays); ?></td>
															<th>Rent Per Days</th>
															<td><?php echo htmlentities($ppd = $result->PricePerDay); ?></td>
														</tr>


														<tr>
															<th colspan="4" style="text-align:center;color:blue">Returning Details</th>
														</tr>
														<tr>
															<th>Vehicle Name</th>
															<td><a href="edit-vehicle.php?id=<?php echo htmlentities($result->vid); ?>"><?php echo htmlentities($result->BrandName); ?> , <?php echo htmlentities($result->VehiclesTitle); ?></td>
															<th>Booking Date</th>
															<td><?php echo htmlentities($result->PostingDate); ?></td>
														</tr>
														<tr>
															<th>From Date</th>
															<td><?php echo htmlentities($result->FromDate); ?></td>
															<th>Return Date</th>
															<td><?php if ($returned) echo htmlentities($result->ReturnDate);
																else echo "Not returned yet" ?></td>
														</tr>
														<tr>
															<th>Total Days</th>
															<td><?php
																if (!$returned) echo htmlentities($tds = $result->totalnodays);
																else {
																	$extraDays = getReturnDaysDifference($BookingNumber = htmlentities($result->BookingNumber), $dbh, "no");
																	$totalDays = htmlentities($tds = $result->totalnodays + $extraDays);
																	echo $totalDays;
																	if ($extraDays != 0) {
																		echo " (incl. $extraDays extra)";
																	}
																}
																?></td>
															<th>Total Fine</th>
															<td><?php if (!$returned) {
																	echo "Not available";
																	$totalFine = 0;
																} else echo "Rs. " . htmlentities($totalFine = $result->FinePerDay * getReturnDaysDifference($BookingNumber, $dbh)); ?></td>
														</tr>



														<tr>
															<th colspan="3" style="text-align:center">Grand Total</th>
															<td><?php echo "Rs. " . htmlentities($grandTotal = $tds * $ppd + $totalFine); ?></td>
														</tr>
														<tr>
															<th>Booking Status</th>
															<td><?php
																if ($result->Status == 0) {
																	echo htmlentities('Not Confirmed yet');
																} else if ($result->Status == 1) {
																	echo htmlentities('Confirmed');
																} else {
																	echo htmlentities('Cancelled');
																}
																?></td>
															<th>Last updation Date</th>
															<td><?php echo htmlentities($result->LastUpdationDate); ?></td>
														</tr>

														<?php if ($result->Status == 0) { ?>
															<tr>
																<td style="text-align:center" colspan="4">
																	<a href="bookig-details.php?aeid=<?php echo htmlentities($result->id); ?>" onclick="return confirm('Do you really want to Confirm this booking')" class="btn btn-primary"> Confirm Booking</a>

																	<a href="bookig-details.php?eid=<?php echo htmlentities($result->id); ?>" onclick="return confirm('Do you really want to Cancel this Booking')" class="btn btn-danger"> Cancel Booking</a>
																</td>
															</tr>
														<?php } ?>
												<?php $cnt = $cnt + 1;
													}
												} ?>

											</tbody>
										</table>
										<form method="post">
											<input name="Submit2" type="submit" class="txtbox4" value="Print" onClick="return f3();" style="cursor: pointer;" />
										</form>

									</div>
								</div>



							</div>
						</div>

					</div>
				</div>
			</div>

			<!-- Loading Scripts -->
			<script src="js/jquery.min.js"></script>
			<script src="js/bootstrap-select.min.js"></script>
			<script src="js/bootstrap.min.js"></script>
			<script src="js/jquery.dataTables.min.js"></script>
			<script src="js/dataTables.bootstrap.min.js"></script>
			<script src="js/Chart.min.js"></script>
			<script src="js/fileinput.js"></script>
			<script src="js/chartData.js"></script>
			<script src="js/main.js"></script>
			<script language="javascript" type="text/javascript">
				function f3() {
					window.print();
				}
			</script>
	</body>

	</html>
<?php } ?>