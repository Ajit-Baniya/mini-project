<?php
session_start();
// error_reporting(0);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include('includes/config.php');
if (strlen($_SESSION['login']) == 0) {
    header('location:index.php');
} else {
    ?>
  <!DOCTYPE HTML>
  <html lang="en">

  <head>

    <title>Car Rental Portal - My Booking</title>
    <!--Bootstrap -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
    <!--Custome Style -->
    <link rel="stylesheet" href="assets/css/style.css" type="text/css">
    <!--OWL Carousel slider-->
    <link rel="stylesheet" href="assets/css/owl.carousel.css" type="text/css">
    <link rel="stylesheet" href="assets/css/owl.transitions.css" type="text/css">
    <!--slick-slider -->
    <link href="assets/css/slick.css" rel="stylesheet">
    <!--bootstrap-slider -->
    <link href="assets/css/bootstrap-slider.min.css" rel="stylesheet">
    <!--FontAwesome Font Style -->
    <link href="assets/css/font-awesome.min.css" rel="stylesheet">

    <!-- SWITCHER -->
    <link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/red.css" title="red" media="all" data-default-color="true" />


    <!-- Fav and touch icons -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/images/favicon-icon/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/images/favicon-icon/apple-touch-icon-114-precomposed.html">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/images/favicon-icon/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="assets/images/favicon-icon/apple-touch-icon-57-precomposed.png">
    <link rel="shortcut icon" href="assets/images/favicon-icon/favicon.png">
    <!-- Google-Font-->
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
  </head>

  <body>


    <!--Header-->
    <?php include('includes/header.php'); ?>
    <!--Page Header-->
    <!-- /Header -->

    <!--Page Header-->
    <section class="page-header profile_page">
      <div class="container">
        <div class="page-header_wrap">
          <div class="page-heading">
            <h1>My Booking</h1>
          </div>
          <ul class="coustom-breadcrumb">
            <li><a href="#">Home</a></li>
            <li>My Booking</li>
          </ul>
        </div>
      </div>
      <!-- Dark Overlay-->
      <div class="dark-overlay"></div>
    </section>
    <!-- /Page Header-->

    <?php

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

    function cancelBooking()
    {
        echo "<script>alert('Hello bitches')</script>";
    }


        function getReturnDaysDifference($BookingNumber, $dbh, $returning)
        {
            $sql0 = "SELECT * from booking where BookingNumber=:BookingNumber";
            $query0 = $dbh->prepare($sql0);
            $query0->bindParam(':BookingNumber', $BookingNumber, PDO::PARAM_STR);
            $query0->execute();
            $result = $query0->fetch(PDO::FETCH_OBJ);
            $today = date('Y-m-d'); // today's date
            if (empty($result->ReturnDate) && $returning == "yes") {
                $sql = "UPDATE booking INNER JOIN vehicles ON (booking.VehicleId=vehicles.id) set booking.ReturnDate=:returndate, vehicles.Quantity = vehicles.Quantity + 1 where booking.BookingNumber=:BookingNumber";
                $query = $dbh->prepare($sql);
                $query->bindParam(':returndate', $today, PDO::PARAM_STR);
                $query->bindParam(':BookingNumber', $BookingNumber, PDO::PARAM_STR);
                $query->execute();
            }

            $sql1 = "SELECT * from booking where BookingNumber=:BookingNumber";
            $query1 = $dbh->prepare($sql1);
            $query1->bindParam(':BookingNumber', $BookingNumber, PDO::PARAM_STR);
            $query1->execute();
            $result = $query1->fetch(PDO::FETCH_OBJ);

            $deadline = getDeadlineDate($BookingNumber, $dbh); // deadline date
            $todayDate = new DateTime($result->ReturnDate);
            $deadlineDate = new DateTime(date("Y-m-d", strtotime($deadline)));
            $datediff = $deadlineDate->diff($todayDate)->format('%r%a');
            if ($datediff <= 0) {
                return 0;
            } else {
                return $datediff;
            }
        }

        function get_xml_node_value($node, $xml)
        {
            if ($xml == false) {
                return false;
            }
            $found = preg_match('#<' . $node . '(?:\s+[^>]+)?>(.*?)' .
              '</' . $node . '>#s', $xml, $matches);
            if ($found != false) {
                return $matches[1];
            }

            return false;
        }

        if (
            isset($_REQUEST['oid']) &&
            isset($_REQUEST['amt']) &&
            isset($_REQUEST['refId']) && isset($_GET["returned"])
        ) {
            $useremail = $_SESSION['login'];
            $BookingNumber = htmlentities($_GET["returned"]);
            $sql = "SELECT vehicles.id as vid,booking.FromDate,booking.ToDate,booking.ReturnDate,vehicles.PricePerDay,vehicles.FinePerDay,DATEDIFF(booking.ToDate,booking.FromDate) as totaldays,booking.BookingNumber from booking join vehicles on booking.VehicleId=vehicles.id join brands on brands.id=vehicles.VehiclesBrand where booking.userEmail=:useremail and booking.BookingNumber=:bookingnumber order by booking.id desc";
            $query = $dbh->prepare($sql);
            $query->bindParam(':useremail', $useremail, PDO::PARAM_STR);
            $query->bindParam(':bookingnumber', $BookingNumber, PDO::PARAM_STR);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_OBJ);

            $extraDays = getReturnDaysDifference($BookingNumber, $dbh, "no");
            $tds = $result->totaldays + $extraDays;
            $ppd = $result->PricePerDay;
            $totalFine = $result->FinePerDay * $extraDays;
            $grandTotal = $tds * $ppd + $totalFine;
        }


    $useremail = $_SESSION['login'];

    $sql = "SELECT * from users where EmailId=:useremail ";
    $query = $dbh->prepare($sql);
    $query->bindParam(':useremail', $useremail, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
    $cnt = 1;
    if ($query->rowCount() > 0) {
        foreach ($results as $result) {
            ?>

        <section class="user_profile inner_pages">
          <div class="container">
            <div class="user_profile_info gray-bg padding_4x4_40">
            <div class="upload_user_logo"> <img src="assets/images/profile<?php echo htmlentities(rand(1, 2));?>.png" alt="image">
              </div>
              <div class="dealer_info">
                <h5><?php echo htmlentities($result->FullName ?? ""); ?></h5>
                <p><?php echo htmlentities($result->Address ?? ""); ?><br>
                  <?php echo htmlentities($result->City ?? ""); ?>&nbsp;<?php echo htmlentities($result->Country ?? "");
        }
    } ?></p>
              </div>
            </div>
            <div class="row">
              <div class="col-md-3 col-sm-3">
                <?php include('includes/sidebar.php'); ?>

                <div class="col-md-8 col-sm-8">
                  <div class="profile_wrap">
                    <h5 class="uppercase underline">My Booikngs </h5>
                    <div class="my_vehicles_list">
                      <ul class="vehicle_listing">
                        <?php
                        $useremail = $_SESSION['login'];
    $sql = "SELECT booking.id as id,vehicles.Vimage1 as Vimage1,vehicles.VehiclesTitle,vehicles.id as vid,brands.BrandName,booking.FromDate,booking.ToDate,booking.ReturnDate,booking.message,booking.Status,vehicles.PricePerDay,vehicles.FinePerDay,DATEDIFF(booking.ToDate,booking.FromDate) as totaldays,booking.BookingNumber  from booking join vehicles on booking.VehicleId=vehicles.id join brands on brands.id=vehicles.VehiclesBrand where booking.userEmail=:useremail order by booking.id desc";
    $query = $dbh->prepare($sql);
    $query->bindParam(':useremail', $useremail, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
    $cnt = 1;
    if ($query->rowCount() > 0) {
        foreach ($results as $result) {
            if (!empty($result->ReturnDate)) {
                $returned = 1;
            } else {
                $returned = 0;
            }
            ?>

                            <li>
                              <h4 style="color:red">Booking No #<?php echo htmlentities($result->BookingNumber); ?></h4>
                              <div class="vehicle_img"> <a href="vehical-details.php?vhid=<?php echo htmlentities($result->vid); ?>"><img src="admin/img/vehicleimages/<?php echo htmlentities($result->Vimage1); ?>" alt="image"></a> </div>
                              <div class="vehicle_title">

                                <h6><a href="vehical-details.php?vhid=<?php echo htmlentities($result->vid); ?>"> <?php echo htmlentities($result->BrandName); ?> , <?php echo htmlentities($result->VehiclesTitle); ?></a></h6>
                                <p><b>From </b> <?php echo htmlentities($result->FromDate); ?> <b>To </b> <?php echo htmlentities($result->ToDate); ?></p>
                                <div style="float: left">
                                  <p><b>Message:</b> <?php echo htmlentities($result->message); ?> </p>
                                </div>
                              </div>
                              <?php if ($result->Status == 1) { ?>
                                <div class="vehicle_status"> <a href="#" class="btn outline btn-xs active-btn">Confirmed</a>
                                  <div class="clearfix"></div>
                                </div>

                              <?php } elseif ($result->Status == 2) { ?>
                                <div class="vehicle_status"> <a href="#" class="btn outline btn-xs">Cancelled</a>
                                    <div class="clearfix"></div>
                                </div>


                              <?php } else { ?>
                                <div class="vehicle_status"> <a href="#" class="btn outline btn-xs">Not Confirm yet</a>
                                <form method="POST" action="cancel.php">
                                        <input type="hidden" name='bookingId' value="<?php echo $result->id ?>" />
                                        <input type='submit' class='btn outline btn-xs active-btn' name='cancelBooking' value='Cancel' />
                                    </form>
                                  <div class="clearfix"></div>
                                </div>
                              <?php } ?>

                            </li>

                            <h5 style="color:blue">Invoice</h5>
                            <table>
                              <tr>
                                <th>Car Name</th>
                                <th>From Date</th>
                                <th><?php if (empty($result->ReturnDate)) {
                                    echo "To Date";
                                    $booked = 1;
                                } else {
                                    echo "Return Date";
                                    $booked = 0;
                                } ?></th>
                                <th>Total Days</th>
                                <th>Rent / Day</th>
                              </tr>
                              <tr>
                                <td><?php echo htmlentities($result->VehiclesTitle); ?>, <?php echo htmlentities($result->BrandName); ?></td>
                                <td><?php echo htmlentities($result->FromDate); ?></td>
                                <td> <?php if ($booked == 1) {
                                    echo htmlentities($result->ToDate);
                                } else {
                                    echo htmlentities($result->ReturnDate);
                                } ?></td>
                                <td>
                                  <?php
                                  if ($booked == 1) {
                                      echo htmlentities($tds = $result->totaldays);
                                  } else {
                                      $extraDays = getReturnDaysDifference($BookingNumber = htmlentities($result->BookingNumber), $dbh, "no");
                                      $totalDays = htmlentities($tds = $result->totaldays + $extraDays);
                                      echo $totalDays;
                                      if ($extraDays != 0) {
                                          echo " (incl. $extraDays extra)";
                                      }
                                  }
            ?>
                                </td>
                                <td> <?php echo "Rs. " . htmlentities($ppd = $result->PricePerDay); ?></td>
                              </tr>

                              <tr>
                                <th colspan="4" style="text-align:center;">Total Fine</th>
                                <td><?php if ($booked == 1) {
                                    echo "Not returned yet";
                                    $totalFine = 0;
                                } else {
                                    echo "Rs. " . htmlentities($totalFine = $result->FinePerDay * getReturnDaysDifference($BookingNumber, $dbh, "no"));
                                } ?></td>
                              </tr>


                              <tr>
                                <th colspan="4" style="text-align:center;"> Grand Total</th>
                                <th><?php echo "Rs. " . htmlentities($grandTotal = $tds * $ppd + $totalFine); ?></th>
                              </tr>
                            </table>
                            <hr />
                          <?php }
        } else { ?>
                          <h5 align="center" style="color:red">No booking yet</h5>
                        <?php } ?>


                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        </section>
        <!--/my-vehicles-->
        <?php include('includes/footer.php'); ?>

        <!-- Scripts -->
        <script src="assets/js/jquery.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        <script src="assets/js/interface.js"></script>

        <!--bootstrap-slider-JS-->
        <script src="assets/js/bootstrap-slider.min.js"></script>
        <!--Slider-JS-->
        <script src="assets/js/slick.min.js"></script>
        <script src="assets/js/owl.carousel.min.js"></script>
  </body>

  </html>
<?php } ?>
