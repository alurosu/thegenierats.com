<?php 
session_start();

$error = "";
if (isset($_POST["user"])) {
  if ($_POST["user"] != "") {
    if ($_POST["pass"] != "") {
      if (strlen($_POST["pass"]) >= 12) {
        if ($_POST["pass"] == $_POST["repass"]) {
          if (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            include "data/parts/config.php";
            // Create a new MySQLi object
            $mysqli = new mysqli($hostname, $username, $password, $database);

            // Check for connection errors
            if ($mysqli->connect_error) {
                $error = "Connection failed: " . $mysqli->connect_error;
            }

            // Get user input from a form
            $username = $_POST['user'];
            $password = hash('sha256', $_POST['pass']);
            $email = $_POST['email'];

            // Prepare the SQL statement
            $stmt = $mysqli->prepare("INSERT INTO users (user, pass, email) VALUES (?, ?, ?)");

            // Bind parameters and execute the statement
            $stmt->bind_param("sss", $username, $password, $email);
            $stmt->execute();

            // Check if the statement was executed successfully
            if ($stmt->affected_rows > 0) {
                $_SESSION["id"] = $stmt->insert_id;
                $_SESSION["user"] = $username;

                // add default rat to registered user
                $stmt = $mysqli->prepare("INSERT INTO rats (user_id) VALUES (?)");
                $stmt->bind_param("s", $_SESSION["id"]);
                $stmt->execute();

                header("Location: https://thegenierats.com/u/".$_SESSION["id"]);
                die();

                $error = "User registered successfully!";
            } else {
                $error = "Error: " . $stmt->error;
            }

            // Close the statement and database connection
            $stmt->close();
            $mysqli->close();
          } else $error = "To which email should we send <br/>the verification code?";
        } else $error = "Make sure you type the same password <br/>in both fields.";
      } else $error = "Password should be longer than 12 characters.";
    } else $error = "Enter a password to protect yourself.";
  } else $error = "What username would you like?";
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register - The Genie Rats</title>
    <?php include 'data/parts/includes.php'; ?>    
  </head>
  <body>
    <?php include 'data/parts/header.php';?>
    <main id="register">
      <img src="/data/images/site/register_success.png" alt="TheGenieRats Register" title="Welcome traveler!">
      <form action="/register#register_form" method="post" id="register_form">
        <h2>Join The Pack</h2>
        <p>Ready to start a new adventure? <br/>Enter your info below:</p>
        <input type="text" name="user" placeholder="Username" value="<?php echo $_POST["user"];?>">
        <input type="password" name="pass" placeholder="Password" value="<?php echo $_POST["pass"];?>">
        <input type="password" name="repass" placeholder="Retype Password" value="<?php echo $_POST["repass"];?>">
        <input type="text" name="email" placeholder="Email" value="<?php echo $_POST["email"];?>">
        <p class="terms">By creating an account, you agree<br/>to The Genie Rats <a href="/terms" target="_blank">Terms</a> and <a href="privacy" target="_blank">Privacy</a> statements</p>
        <div class="form-footer">
            <a href="/login">Login</a>
            <button>Register</button>
        </div>
      </form>
    </main>
    <?php include 'data/parts/footer.php'; ?>
  </body>
</html>