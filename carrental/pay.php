<body onload="document.form.submit()">
<form name="form" action="https://uat.esewa.com.np/epay/main" method="POST">
    <input value="<?php echo htmlentities($_GET["total"]);?>" name="tAmt" type="hidden">
    <input value="<?php echo htmlentities($_GET["total"]);?>" name="amt" type="hidden">
    <input value="0" name="txAmt" type="hidden">
    <input value="0" name="psc" type="hidden">
    <input value="0" name="pdc" type="hidden">
    <input value="epay_payment" name="scd" type="hidden">
    <input value="<?php echo md5(htmlentities($_GET["returning"])); ?>" name="pid" type="hidden">
    <input value="<?php echo "http://".getenv('HTTP_HOST').substr($_SERVER['REQUEST_URI'], 0, strrpos($_SERVER['REQUEST_URI'], '/') + 1).'my-booking.php?q=su&returned='.htmlentities($_GET["returning"]) ?>" type="hidden" name="su">
    <input value="<?php echo "http://".getenv('HTTP_HOST').substr($_SERVER['REQUEST_URI'], 0, strrpos($_SERVER['REQUEST_URI'], '/') + 1).'payment_failed.php?q=fu' ?>" type="hidden" name="fu">
</form>
</body>