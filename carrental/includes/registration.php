<?php
error_reporting(1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once "vendor/autoload.php";


if (isset($_POST['signup'])) {
    $fname=$_POST['fullname'];
    $email=$_POST['emailid'];
    $mobile=$_POST['mobileno'];
    $password=md5($_POST['password']);
    $enc = "aes-128-gcm";
    $token = openssl_encrypt($email, $enc, time(), 0, openssl_random_pseudo_bytes(openssl_cipher_iv_length($enc)), $tag);
    $sql="INSERT INTO  users(FullName,EmailId,ContactNo,Password,Token,Verified) VALUES(:fname,:email,:mobile,:password,:token,0)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':fname', $fname, PDO::PARAM_STR);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':mobile', $mobile, PDO::PARAM_STR);
    $query->bindParam(':password', $password, PDO::PARAM_STR);
    $query->bindParam(':token', $token, PDO::PARAM_STR);
    $query->execute();
    $lastInsertId = $dbh->lastInsertId();
    if ($lastInsertId) {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'be2019se673@gces.edu.np';
        $mail->Password   = 'YOUR_PASSWORD_HERE';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;
        $mail->setFrom('be2019se673@gces.edu.np');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = "Car Rental Verification";
        $mail->Body = "<html><head><title>Car Rental Verification</title></head><body><p>Dear user,</p><p>Thank you for joining the Car Rental!</p><p>Your Account Verification Token: $token</p><a href='http://localhost:9000/verification.php?token=".urlencode($token)."'>Verification Link</a></p><p>Thanks,<br>Car Rental</p></body></html>";
        try {
            $mail->send();
            echo "<script>alert('Registration successfull. Now you can login. Check email for verification.');</script>";
        } catch (Exception $e) {
            echo "<script>alert('Something went wrong. Please try again');</script>";
            echo "Mailer Error: " . $mail->ErrorInfo;
        }
    }
}
?>


<script>
function checkEmailAvailability() {
$("#loaderIcon").show();
jQuery.ajax({
url: "check_availability.php?email=1",
data:'emailid='+$("#emailid").val(),
type: "POST",
success:function(data){
$("#user-availability-status").html(data);
$("#loaderIcon").hide();
},
error:function (){}
});
}
function checkPhoneAvailability() {
$("#loaderIcon").show();
jQuery.ajax({
url: "check_availability.php?phone=1",
data:'mobileno='+$("#mobileno").val(),
type: "POST",
success:function(data){
$("#phone-availability-status").html(data);
$("#loaderIcon").hide();
},
error:function (){}
});
}
</script>
<script type="text/javascript">
function valid()
{
if(document.signup.password.value!= document.signup.confirmpassword.value)
{
alert("Password and Confirm Password Field do not match  !!");
document.signup.confirmpassword.focus();
return false;
}
return true;
}
</script>
<div class="modal fade" id="signupform">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">Sign Up</h3>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="signup_wrap">
            <div class="col-md-12 col-sm-6">
              <form  method="post" name="signup" onSubmit="return valid();">
                <div class="form-group">
                  <input type="text" class="form-control" name="fullname" pattern="([a-zA-Z]{2,}\s?\b){2,}" placeholder="Full Name" required="required">
                </div>
                      <div class="form-group">
                  <input type="text" class="form-control" name="mobileno" pattern="\d{10}" id="mobileno" onBlur="checkPhoneAvailability()" placeholder="Mobile Number" maxlength="10" required="required">
                   <span id="phone-availability-status" style="font-size:12px;"></span> 
                </div>
                <div class="form-group">
                  <input type="email" class="form-control" name="emailid" id="emailid" onBlur="checkEmailAvailability()" placeholder="Email Address" required="required">
                   <span id="user-availability-status" style="font-size:12px;"></span> 
                </div>
                <div class="form-group">
                  <input type="password" class="form-control" name="password" placeholder="Password" required="required">
                </div>
                <div class="form-group">
                  <input type="password" class="form-control" name="confirmpassword" placeholder="Confirm Password" required="required">
                </div>
                <div class="form-group checkbox">
                  <input type="checkbox" id="terms_agree" required="required" checked="">
                  <label for="terms_agree">I Agree with <a href="#">Terms and Conditions</a></label>
                </div>
                <div class="form-group">
                  <input type="submit" value="Sign Up" name="signup" id="submit" class="btn btn-block">
                </div>
              </form>
            </div>
            
          </div>
        </div>
      </div>
      <div class="modal-footer text-center">
        <p>Already got an account? <a href="#loginform" data-toggle="modal" data-dismiss="modal">Login Here</a></p>
      </div>
    </div>
  </div>
</div>
