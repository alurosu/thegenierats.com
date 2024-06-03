<?php 
session_start();

$notice = "";
if ($_GET["action"]=="logout") {
  session_unset();
  $notice = "You have successfully logged out.";
}
if ($_GET["login"]=="required") {
  $notice = "You need to login before doing that.";
}

$rediurl = $_SERVER['HTTP_REFERER'];
if (isset($_POST["rediurl"]))
    $rediurl = $_POST["rediurl"];

$error = "";
if (isset($_POST["user"])) {
  if ($_POST["user"] != "") {
    if ($_POST["pass"] != "") {
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

      // Prepare the SQL statement
      $stmt = $mysqli->prepare("SELECT id, pass, coins FROM users WHERE user = ?");

      // Bind parameters and execute the statement
      $stmt->bind_param("s", $username);
      $stmt->execute();

      // Check if the statement was executed successfully
      $result = $stmt->get_result();
      $row = $result->fetch_assoc();
      
      // Close the statement and database connection
      $stmt->close();
      $mysqli->close();
      
      if ($row["pass"] == $password) {
        $_SESSION["id"] = $row["id"];
        $_SESSION["coins"] = $row["coins"];
        $_SESSION["user"] = $username;

        if ($rediurl == "https://thegenierats.com/")
            header("Location: https://thegenierats.com/u/".$_SESSION["id"]);
        else
            header("Location: ".$rediurl);
        die();

        $error = "User login successfully!";
      } else {
        $error = "Wrong username or password. Try again.";
      }
    } else $error = "What's the password?";
  } else $error = "Do you have a username?";
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login - The Genie Rats</title>
    <link rel="stylesheet" href="/data/main.css">
    <link rel="icon" href="/data/thegenierats.ico" type="image/x-icon">
  </head>
  <body>
    <?php include 'data/parts/header.php'; ?>
    <main id="login">
      <div class="center_image">
        <form action="/login" method="post">
          <h2>Welcome to The Pack</h2>
          <p>Submit your info to enter.<br/>If you don't have an account, <a href="/register">create one for free</a>.</p>
          <input type="text" name="user" placeholder="Username" value="<?php echo $_POST["user"];?>">
          <input type="password" name="pass" placeholder="Password">
          <input type="hidden" name="rediurl" value="<?php echo $rediurl; ?>" />
          <div class="form-footer">
            <a href="/forgot">Forgot password?</a>
            <button>Login</button>
          </div>
        </form>
        <img src="/data/images/site/login_gate.png" alt="TheGenieRats Login Gate" title="Are you one of us?">
      </div>
    </main>
    <?php include 'data/parts/footer.php'; ?>
  </body>
</html>