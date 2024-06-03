<?php 
session_start();
if(!isset($_SESSION["id"])) {
  header("Location: https://thegenierats.com/login/?login=required");
  die();
}

include "../../data/parts/config.php";
// Create a new MySQLi object
$mysqli = new mysqli($hostname, $username, $password, $database);

// Check for connection errors
if ($mysqli->connect_error) {
  $error = "Connection failed: " . $mysqli->connect_error;
}
// Prepare the SQL statement
$stmt = $mysqli->prepare("SELECT id, title, ends_at FROM quests ORDER BY id DESC LIMIT 1");

// Bind parameters and execute the statement
$stmt->execute();

// Check if the statement was executed successfully
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$quest_id = $row["id"];
$title = $row["title"];
$ends_at = $row["ends_at"];

if (time()>$ends_at) {
    header("Location: https://thegenierats.com/challenges");
    die();
}

$platform = "";
$link = "";
$error = "";
$title = "test";
if (isset($_POST["video"])) {
  if ($_POST["title"] != "") {
    $title = $_POST["title"];
  } else $error = "Please enter a short title for the video.";

  if (strpos($_POST["video"], 'youtube') !== false || strpos($_POST["video"], 'youtu')) {
    // it's a youtube short
    $platform = "youtube";
    function parse_yturl($url) {
      $pattern = '#^(?:https?://)?(?:www\.)?(?:youtu\.be/|youtube\.com(?:/embed/|/shorts/|/v/|/watch\?v=|/watch\?.+&v=))([\w-]{11})(?:.+)?$#x';
      preg_match($pattern, $url, $matches);
      return (isset($matches[1])) ? $matches[1] : false;
    }
    $link = "https://www.youtube.com/embed/" . parse_yturl($_POST["video"]);
  } else if (strpos($_POST["video"], 'tiktok') !== false) {
    // it's a tiktok video
    $platform = "tiktok";
    $link = explode("?",$_POST["video"]);
    $link = $link[0];
  } else if (strpos($_POST["video"], 'instagram') !== false) {
    // it's an instagram reel
    $platform = "instagram";
    $link = explode("?",$_POST["video"]); // https://www.instagram.com/reel/CiXOlLODC4_/?utm_source=ig_web_copy_link&igshid=MzRlODBiNWFlZA==
    $link = $link[0];
  } else $error = "Please enter a valid link.";

  if ($error == "") {
    $stmt = $mysqli->prepare("INSERT INTO `quest_entries` (`user_id`, `quest_id`, `platform`, `title`, `link`) VALUES (?,?,?,?,?)");
    $stmt->bind_param("iisss", $_SESSION["id"], $quest_id, $platform, $title, $link);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        $stmt = $mysqli->prepare("UPDATE users SET challenges = challenges + 1 WHERE id = ?");
        $stmt->bind_param("i", $_SESSION["id"]);
        $stmt->execute();
        header("Location: https://thegenierats.com/challenges/v/".$stmt->insert_id);
        die();
    } else {
        $error = "Error: " . $stmt->error;
    }
  }
}

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
    <title>Enter the Challenge - The Genie Rats</title>
    <?php include '../../data/parts/includes.php'; ?>    
  </head>
  <body>
    <?php include '../../data/parts/header.php'; ?>
    <main id="login">
      <div class="center_image">
        <form action="/challenges/add/" method="post">
            <h2>Are you ready?</h2>
            <p>Create a short video that proves you completed the challenge.<br/><a href="https://thegenierats.substack.com/p/how-challenges-work" target="_blank">Learn more</a></p>
            <input type="text" name="title" placeholder="Short title" value="<?php echo $_POST["title"]; ?>">
            <input type="text" name="video" placeholder="Link to Video" value="<?php echo $_POST["video"]; ?>">
            <div class="form-footer">
                <a href="/challenges">Cancel</a>
                <button>Add Video</button>
            </div>
            <p class="add-challenge-details">You can use TikTok, Instagram Reels or YouTube Shorts.</p>
        </form>
      </div>
    </main>
    <?php include '../../data/parts/footer.php'; ?>
  </body>
</html>