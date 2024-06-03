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
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Password Recovery - The Genie Rats</title>
    <link rel="stylesheet" href="/data/main.css">
    <link rel="icon" href="/data/thegenierats.ico" type="image/x-icon">
  </head>
  <body>
    <?php include 'data/parts/header.php'; ?>
    <main id="register">
      <img src="/data/images/site/forgot_password.png" alt="TheGenieRats forgot password" title="Are you lost?">
      <form action="" method="post">
        <h2>Forgot your password?</h2>
        <p>Enter your email and we'll help you out.<br/>If you don't have an account, <a href="/register">create one for free</a>.</p>
        <input type="text" name="email" placeholder="Email" value="<?php echo $_POST["email"];?>">
        <div class="form-footer">
            <a href="/login">Back to Login</a>
            <button>Recover</button>
        </div>
      </form>
    </main>
    <?php include 'data/parts/footer.php'; ?>
  </body>
</html>