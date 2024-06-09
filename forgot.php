<?php 
session_start();

$error = "";
if (isset($_POST["email"])) {
  if (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    // generate code
    $code = hash('sha256', time().rand());

    // update in database
    include "data/parts/config.php";
    $mysqli = new mysqli($hostname, $username, $password, $database);

    if ($mysqli->connect_error) {
        $error = "Connection failed: " . $mysqli->connect_error;
    }

    $stmt = $mysqli->prepare("UPDATE users SET code = ? WHERE email = ?");
    $stmt->bind_param("ss", $code, $_POST["email"]);
    $stmt->execute();

    if ($stmt->affected_rows == 1) {
        // send email
        $link = "http://thegenierats.com/forgot?code=".$code;

        $to = $_POST["email"];

        $subject = 'Password Reset Instructions - The Genie Rats';

        $headers  = "From: office@thegenierats.com\r\n";
        $headers .= "Reply-To: office@thegenierats.com\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        $message = file_get_contents("data/parts/email-templates/forgot-password.php");
        $message = str_replace("[link]", $link, $message);

        $success = mail($to, $subject, $message, $headers);
        if (!$success) {
            $error = error_get_last()['message'];
        } else $notice = "We sent you a recovery email. Check your inbox.";
    } else $error = "We don't know anyone with that email.";

    $stmt->close();
    $mysqli->close();
  } else $error = "Please enter a valid email.";
}

if (isset($_GET["code"])) {
  // verify if exists
  if (isset($_POST["npass"])) {
    if (strlen($_POST["npass"]) >= 6) {
      if ($_POST["npass"] == $_POST["npass2"]) {
        $pass = hash('sha256', $_POST['npass']);
        // update in database
        include "data/parts/config.php";
        $mysqli = new mysqli($hostname, $username, $password, $database);

        if ($mysqli->connect_error) {
            $error = "Connection failed: " . $mysqli->connect_error;
        }
        // Prepare the SQL statement
        $stmt = $mysqli->prepare("UPDATE users SET pass = ? WHERE code = ?");
  
        // Bind parameters and execute the statement
        $stmt->bind_param("ss", $pass, $_GET["code"]);
        $stmt->execute();

        $stmt->close();
        $mysqli->close();
        
        $notice = "You changed the account password.";
      } else $error = "Your new password doesn't match.";
    } else $error = "New passord is too short.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Forgot Your Password? - The Genie Rats</title>
    <meta name="description" content="Don't let a forgotten password slow you down. Reset your password and quickly get back to conquering challenges and earning cheese.">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="/data/main.css">
    <link rel="icon" href="/data/thegenierats.ico" type="image/x-icon">
  </head>
  <body>
    <?php include 'data/parts/header.php'; ?>
    <main id="register">
      <img src="/data/images/site/forgot_password.png" alt="TheGenieRats forgot password" title="Are you lost?">
      <?php if (isset($_GET['code'])) { ?>
        <form action="" id="password" method="post">
          <h3>Change Password</h3>
          <p>Your new password should be at least 6 characters long.</p>
          <input type="password" name="npass" placeholder="New Password" >
          <input type="password" name="npass2" placeholder="New Password Again" >
          <button>Change</button>
      </form>
      <?php } else { ?>
        <form action="" method="post">
          <h2>Forgot your password?</h2>
          <p>Enter your email and we'll help you out.<br/>If you don't have an account, <a href="/register">create one for free</a>.</p>
          <input type="text" name="email" placeholder="Email" value="<?php echo $_POST["email"];?>">
          <div class="form-footer">
              <a href="/login">Back to Login</a>
              <button>Recover</button>
          </div>
        </form>
      <?php } ?>
    </main>
    <?php include 'data/parts/footer.php'; ?>
  </body>
</html>