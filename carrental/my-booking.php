<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Check if the user is logged in, redirect to login page if not
if(strlen($_SESSION['login']) == 0) {
    header('location:index.php');
    exit;
}

// Retrieve the parameters from the GET request
$status = $_GET['status'] ?? null;
$transactionId = $_GET['transaction_id'] ?? null;
$fromDate = $_GET['from_date'] ?? null;
$toDate = $_GET['to_date'] ?? null;
$message = $_GET['message'] ?? null;

// Fetch user details from the database
$useremail = $_SESSION['login'];
$sql = "SELECT * from tblusers WHERE EmailId=:useremail";
$query = $dbh->prepare($sql);
$query->bindParam(':useremail', $useremail, PDO::PARAM_STR);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);
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
    <link rel="stylesheet" id="switcher-css" type="text/css" href="assets/switcher/css/switcher.css" media="all" />
    <link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/red.css" title="red" media="all" data-default-color="true" />
    <link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/orange.css" title="orange" media="all" />
    <link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/blue.css" title="blue" media="all" />
    <link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/pink.css" title="pink" media="all" />
    <link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/green.css" title="green" media="all" />
    <link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/purple.css" title="purple" media="all" />
    <!-- Fav and touch icons -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/images/favicon-icon/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/images/favicon-icon/apple-touch-icon-114-precomposed.html">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/images/favicon-icon/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="assets/images/favicon-icon/apple-touch-icon-57-precomposed.png">
    <link rel="shortcut icon" href="assets/images/favicon-icon/favicon.png">
    <!-- Google-Font-->
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900" rel="stylesheet">
</head>
<body>

<!-- Start Switcher -->
<?php include('includes/colorswitcher.php');?>
<!-- /Switcher -->  

<!--Header-->
<?php include('includes/header.php');?>
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
    <div class="dark-overlay"></div>
</section>
<!-- /Page Header--> 

<?php if ($query->rowCount() > 0): ?>
    <?php foreach ($results as $result): ?>
        <section class="user_profile inner_pages">
            <div class="container">
                <div class="user_profile_info gray-bg padding_4x4_40">
                    <div class="upload_user_logo"> <img src="assets/images/dealer-logo.jpg" alt="image"></div>
                    <div class="dealer_info">
                        <h5><?php echo htmlentities($result->FullName ?? ''); ?></h5>
                        <p><?php echo htmlentities($result->Address ?? ''); ?><br>
                        <?php echo htmlentities($result->City ?? ''); ?>&nbsp;<?php echo htmlentities($result->Country ?? ''); ?></p>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-3 col-sm-3">
                        <?php include('includes/sidebar.php'); ?>
                    </div>
                    <div class="col-md-8 col-sm-8">
                        <div class="profile_wrap">
                            <h5 class="uppercase underline">My Bookings </h5>
                            <div class="my_vehicles_list">
                                <ul class="vehicle_listing">
                                    <?php 
                                    $sql = "SELECT tblvehicles.Vimage1 as Vimage1, tblvehicles.VehiclesTitle, tblvehicles.id as vid, 
                                            tblbrands.BrandName, tblbooking.FromDate, tblbooking.ToDate, tblbooking.message, 
                                            tblbooking.Status, tblvehicles.PricePerDay, DATEDIFF(tblbooking.ToDate, tblbooking.FromDate) as totaldays, 
                                            tblbooking.BookingNumber  
                                            FROM tblbooking 
                                            JOIN tblvehicles ON tblbooking.VehicleId=tblvehicles.id 
                                            JOIN tblbrands ON tblbrands.id=tblvehicles.VehiclesBrand 
                                            WHERE tblbooking.userEmail=:useremail 
                                            ORDER BY tblbooking.id DESC";
                                    $query = $dbh->prepare($sql);
                                    $query->bindParam(':useremail', $useremail, PDO::PARAM_STR);
                                    $query->execute();
                                    $bookings = $query->fetchAll(PDO::FETCH_OBJ);

                                    if ($query->rowCount() > 0): 
                                        foreach ($bookings as $booking): 
                                    ?>
                                            <li>
                                                <h4 style="color:red">Booking No #<?php echo htmlentities($booking->BookingNumber); ?></h4>
                                                <div class="vehicle_img">
                                                    <a href="vehical-details.php?vhid=<?php echo htmlentities($booking->vid); ?>">
                                                        <img src="admin/img/vehicleimages/<?php echo htmlentities($booking->Vimage1); ?>" alt="image">
                                                    </a>
                                                </div>
                                                <div class="vehicle_title">
                                                    <h6><a href="vehical-details.php?vhid=<?php echo htmlentities($booking->vid); ?>">
                                                        <?php echo htmlentities($booking->BrandName); ?> , <?php echo htmlentities($booking->VehiclesTitle); ?></a></h6>
                                                    <p><b>From</b> <?php echo htmlentities($booking->FromDate); ?> <b>To</b> <?php echo htmlentities($booking->ToDate); ?></p>
                                                    <p><b>Message:</b> <?php echo htmlentities($booking->message); ?></p>
                                                </div>

                                                <?php if ($booking->Status == 1): ?>
                                                    <div class="vehicle_status"> <a href="#" class="btn outline btn-xs active-btn">Confirmed</a></div>
                                                <?php elseif ($booking->Status == 2): ?>
                                                    <div class="vehicle_status"> <a href="#" class="btn outline btn-xs">Cancelled</a></div>
                                                <?php else: ?>
                                                    <div class="vehicle_status"> <a href="#" class="btn outline btn-xs">Not Confirm yet</a></div>
                                                <?php endif; ?>
                                                

                                                <h5 style="color:blue">Invoice</h5>
                                                <table>
                                                    <tr>
                                                        <th>Car Name</th>
                                                        <th>From Date</th>
                                                        <th>To Date</th>
                                                        <th>Total Days</th>
                                                        <th>Rent / Day</th>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo htmlentities($booking->VehiclesTitle) . ', ' . htmlentities($booking->BrandName); ?></td>
                                                        <td><?php echo htmlentities($booking->FromDate); ?></td>
                                                        <td><?php echo htmlentities($booking->ToDate); ?></td>
                                                        <td><?php echo htmlentities($totalDays = $booking->totaldays); ?></td>
                                                        <td><?php echo htmlentities($rentPerDay = $booking->PricePerDay); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="4" style="text-align:center;">Grand Total</th>
                                                        <th><?php echo htmlentities($totalDays * $rentPerDay); ?></th>
                                                    </tr>
                                                </table>
                                                <hr />

                                                <div style="text-align: center; margin-top: 20px;">
                                                    <?php if ($booking->Status == 1): ?>
                                                        <?php if ($status != 'success'): // Only show the "Pay Now" button if payment is not successful ?>
                                                            <a href="user_info.php?amount=<?php echo htmlentities($totalDays * $rentPerDay); ?>" class="btn btn-primary">Pay Now</a>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <h5>No bookings found.</h5>
                                    <?php endif; ?>

                                    <?php if ($status == 'success'): ?>
                                        <div class="success-message">
                                            <p><strong>Payment Successful!</strong></p>
                                        </div>
                                    <?php else: ?>
                                        <div class="error-message">
                                            <p>Something went wrong. Please try again later.</p>
                                        </div>
                                    <?php endif; ?>

                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php endforeach; ?>
<?php endif; ?>

<?php include('includes/footer.php');?>
<?php include('includes/login.php');?>
<?php include('includes/registration.php');?>
<?php include('includes/forgotpassword.php');?>
<!-- Scripts --> 
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script> 
<script src="assets/js/interface.js"></script> 
<!--Switcher-->
<script src="assets/switcher/js/switcher.js"></script>
<!--bootstrap-slider-JS--> 
<script src="assets/js/bootstrap-slider.min.js"></script> 
<!--Slider-JS--> 
<script src="assets/js/slick.min.js"></script> 
<script src="assets/js/owl.carousel.min.js"></script>
</body>
</html>