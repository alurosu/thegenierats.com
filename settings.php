<?php 
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

session_start();
if(!isset($_SESSION["id"])) {
    header("Location: https://thegenierats.com/login/?login=required");
    die();
}

$id=$_SESSION["id"];

include "data/parts/config.php";
// Create a new MySQLi object
$mysqli = new mysqli($hostname, $username, $password, $database);

// Check for connection errors
if ($mysqli->connect_error) {
  $error = "Connection failed: " . $mysqli->connect_error;
}

// Check if image file is a actual image or fake image
$error = "";
$notice = "";
if(isset($_POST["submit"])) {
  $target_dir = "data/images/users/".$id."/";
  $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
  $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
  $target_file = $target_dir."avatar.".$imageFileType;

  $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
  if($check === false) 
    $error = "Your file is not an image.";

  // Check file size
  if ($_FILES["fileToUpload"]["size"] > 5000000)
    $error = "Your file is larger than 5 MB.";

  // Allow certain file formats
  if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" )
    $error = "Only JPG, JPEG, PNG & GIF files are allowed.";

  // Check if $uploadOk is set to 0 by an error
  if ($error == "") {
    // Create directory if it does not exist
    if(!is_dir($target_dir)) {
      mkdir($target_dir);
    }
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
      $target_file = "/data/images/users/".$id."/avatar.".$imageFileType;
      // Prepare the SQL statement
      $stmt = $mysqli->prepare("UPDATE users SET avatar = ? WHERE id = ?");

      // Bind parameters and execute the statement
      $stmt->bind_param("ss", $target_file, $id);
      $stmt->execute();

      $notice = "You changed your avatar.";
    } else $error = "There was an error uploading your file.";
  }
} else if (isset($_POST["email"])) {
    $email = $_POST["email"];
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Prepare the SQL statement
        $stmt = $mysqli->prepare("UPDATE users SET email = ? WHERE id = ?");

        // Bind parameters and execute the statement
        $stmt->bind_param("si", $email, $id);
        $stmt->execute();
        
        if ($stmt->errno == 1062)
          $error = "Email linked to another account.";
        else
          $notice = "You changed your recovery email.";
    } else $error = "Invalid email format.";
} else if (isset($_POST["wallet_stargaze"])) {
    $wallet_stargaze = $_POST["wallet_stargaze"];
    if (strpos($wallet_stargaze, "stars") !== false) {
        // Prepare the SQL statement
        $stmt = $mysqli->prepare("UPDATE users SET wallet_stargaze = ? WHERE id = ?");

        // Bind parameters and execute the statement
        $stmt->bind_param("si", $wallet_stargaze, $id);
        $stmt->execute();
        
        $notice = "You changed your stargaze wallet.";
    } else $error = "Invalid stargaze wallet address.";
} else if (isset($_POST["npass"])) {
  if (strlen($_POST["npass"]) >= 12) {
    if ($_POST["npass"] == $_POST["npass2"]) {
      $pass = hash('sha256', $_POST['npass']);
      // Prepare the SQL statement
      $stmt = $mysqli->prepare("UPDATE users SET pass = ? WHERE id = ?");

      // Bind parameters and execute the statement
      $stmt->bind_param("si", $pass, $id);
      $stmt->execute();
      
      $notice = "You changed the account password.";
    } else $error = "Your new password doesn't match.";
  } else $error = "New passord is too short.";
}

// Prepare the SQL statement
$stmt = $mysqli->prepare("SELECT user, email, wallet_stargaze, avatar, god FROM users WHERE id = ?");

// Bind parameters and execute the statement
$stmt->bind_param("i", $id);
$stmt->execute();

// Check if the statement was executed successfully
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$user = $row["user"];
$email = $row["email"];
$wallet_stargaze = $row["wallet_stargaze"];
$avatar = $row["avatar"];
$god = $row["god"];

// Close the statement and database connection
$stmt->close();
$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Account Settings - The Genie Rats</title>
    <?php include 'data/parts/includes.php'; ?>    
  </head>
  <body>
    <?php include 'data/parts/header.php'; ?>
    <main class="user-page">
        <div class="profile-name">
            <img src="<?php echo $avatar; ?>" alt="<?php echo $user;?>'s avatar" title="<?php echo $user;?>'s avatar" <?php if (isset($_SESSION["user"]) && $_SESSION["user"] == $user) echo 'class="change-avatar-trigger"'; ?>>
            <div>
                <?php if ($god) { ?> 
                    <a href="https://thegenierats.com/discord" target="_blank">
                        <h2 class="genie"><?php echo $user;?></h2>
                        <span>Genie</span>
                    </a>
                <?php } else {?>
                    <h2><?php echo $user;?></h2>
                    <span>Trainer</span>
                <?php } ?>
            </div>
        </div>
        <div class="profile-menu py-3">
            <h2>Account Settings</h2>
            <ul class="m-0">
                <li data-show="email">
                    Email
                </li>
                <li data-show="password">
                    Password
                </li>
                <li data-show="wallet">
                    Wallet
                </li>
                <li class="change-avatar-trigger">
                    Avatar
                </li>
                <li>
                    <a href="/login?action=logout">Logout</a>
                </li>
            </ul>
        </div>
        <div class="settings mt-3">  
            <form action="" method="post" enctype="multipart/form-data" id="change-avatar">
                <input type="file" name="fileToUpload" id="fileToUpload"><input type="submit" value="Upload Image" name="submit">
            </form>
            <form action="/settings#email" id="email" method="post" <?php if (isset($_POST["email"])) echo 'style="display:block;"';?>>
                <h3>Change Email</h3>
                <p>You'll need access to this address, if you lose your password.</p>
                <input type="text" name="email" placeholder="email@provider.com" value="<?php echo $email; ?>">
                <button>Save</button>
            </form>
            <form action="/settings#password" id="password" method="post"  <?php if (isset($_POST["hpass"])) echo 'style="display:block;"';?>>
                <h3>Change Password</h3>
                <p>Your new password should be at least 12 characters long.</p>
                <input type="password" name="npass" placeholder="New Password" >
                <input type="password" name="npass2" placeholder="New Password Again" >
                <input type="hidden" name="hpass" value="1">
                <button>Change</button>
            </form>
            <form action="/settings#wallet" id="wallet" method="post" <?php if (isset($_POST["wallet_stargaze"])) echo 'style="display:block;"';?>>
                <h3>Crypto Wallet</h3>
                <p>We'll send you NFTs to this <a href="https://www.stargaze.zone/" target="_blank">Stargaze</a> address. Make sure it's the right one.</p>
                <input type="text" name="wallet_stargaze" placeholder="stars1ug4n..." value="<?php echo $wallet_stargaze; ?>">
                <button>Save</button>
            </form>
        </div>
    </main>
    <?php include 'data/parts/footer.php'; ?>
  </body>
</html>
