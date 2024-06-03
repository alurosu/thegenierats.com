<?php 
if (!is_numeric($_GET["id"]) || $_GET["id"]<=0) {
    header("Location: https://thegenierats.com/challenges");
    die();
}
session_start();

$action = "";
if ($_GET["action"] != "")
    $action = $_GET["action"];

if ($action != "" && !isset($_SESSION["id"])) {
    header("Location: https://thegenierats.com/login/?login=required");
    die();
}

$id = $_GET["id"];

include "../../data/parts/config.php";
// Create a new MySQLi object
$mysqli = new mysqli($hostname, $username, $password, $database);

// Check for connection errors
if ($mysqli->connect_error) {
  $error = "Connection failed: " . $mysqli->connect_error;
}
// Prepare the SQL statement
$stmt = $mysqli->prepare("SELECT q.title AS quest, qe.quest_id AS quest_id, qe.platform AS platform, qe.title AS title, qe.link AS link, u.id AS user_id, u.user AS user FROM quest_entries qe, users u, quests q WHERE u.id = qe.user_id AND q.id = qe.quest_id AND qe.id = ?");

// Bind parameters and execute the statement
$stmt->bind_param("i", $id);
$stmt->execute();

// Check if the statement was executed successfully
$result = $stmt->get_result();
$info = $result->fetch_assoc();

$stmt = $mysqli->prepare("SELECT id FROM quest_entries WHERE quest_id = ? AND id < ? ORDER BY id DESC LIMIT 1");
$stmt->bind_param("ii", $info["quest_id"], $id);
$stmt->execute();
$result = $stmt->get_result();
$next = $result->fetch_assoc();


if (isset($_SESSION["id"])) {
    if ($action == "like") {
        $verification = $_SESSION["id"]."_".$id;
        $stmt = $mysqli->prepare("INSERT INTO votes (user_id, quest_entry_id, verification) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $_SESSION["id"], $id, $verification);
        $stmt->execute();

        $stmt = $mysqli->prepare("UPDATE users SET votes = votes+1 WHERE id = ?");
        $stmt->bind_param("i", $info["user_id"]);
        $stmt->execute();

        $stmt = $mysqli->prepare("UPDATE quest_entries SET votes = votes+1 WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $action = "dislike";
    } else if ($action == "dislike") {
        $stmt = $mysqli->prepare("DELETE FROM votes WHERE user_id = ? AND quest_entry_id = ?");
        $stmt->bind_param("ii", $_SESSION["id"], $id);
        $stmt->execute();

        $stmt = $mysqli->prepare("UPDATE users SET votes = votes-1 WHERE id = ?");
        $stmt->bind_param("i", $info["user_id"]);
        $stmt->execute();

        $stmt = $mysqli->prepare("UPDATE quest_entries SET votes = votes-1 WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $action = "like";
    } else {
        $stmt = $mysqli->prepare("SELECT id FROM votes WHERE user_id = ? AND quest_entry_id = ?");
        $stmt->bind_param("ii", $_SESSION["id"], $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $vote = $result->fetch_assoc();
        if ($vote) 
            $action = "dislike";
        else
            $action = "like";
    }
} else $action = "like";

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
    <title><?php echo $info["title"]; ?> - Challenge Entry <?php echo "#".$_GET["id"]; ?></title>
    <link rel="stylesheet" href="/data/video.css?v=6">
    <link rel="icon" href="/data/thegenierats.ico" type="image/x-icon">
  </head>
  <body>
    <header>
        <div>
            <h1><?php echo $info["quest"];?></h1>
            <a href="/challenges">
                <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 384 512"><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg>
            </a>
        </div>
    </header>
    <main class="<?php echo $info["platform"]; ?>">
        <?php if ($info["platform"] == "youtube") { ?>
            <iframe src="<?php echo $info["link"];?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
        <?php } else if ($info["platform"] == "instagram") { ?>
            <blockquote class="instagram-media" data-instgrm-permalink="<?php echo $info["link"];?>?utm_source=ig_embed&amp;utm_campaign=loading" data-instgrm-version="14" style=" background:#FFF; border:0; border-radius:3px; box-shadow:0 0 1px 0 rgba(0,0,0,0.5),0 1px 10px 0 rgba(0,0,0,0.15); margin: 1px; padding:0;"></blockquote> <script async src="//www.instagram.com/embed.js"></script>
        <?php } else if ($info["platform"] == "tiktok") { ?>
            <blockquote class="tiktok-embed" cite="https://www.tiktok.com/@creativemonkeyz/video/7271194303974690081" data-video-id="7271194303974690081" > <section> <a target="_blank" title="" href="https://www.tiktok.com/@creativemonkeyz?refer=embed"></a> <p></p> <a target="_blank" title="" href="https://www.tiktok.com/music/original-sound-CreativeMonkeyz-7271194789516036896?refer=embed"></a> </section> </blockquote> <script async src="https://www.tiktok.com/embed.js"></script>
        <?php } ?>
    </main>
    <nav>
        <div>
            <a href="/u/<?php echo $info["user_id"];?>" class="user" target="_blank">by <span><?php echo $info["user"];?></span></a>
            <div>
                <svg style="display: none;" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><path d="M47.6 300.4L228.3 469.1c7.5 7 17.4 10.9 27.7 10.9s20.2-3.9 27.7-10.9L464.4 300.4c30.4-28.3 47.6-68 47.6-109.5v-5.8c0-69.9-50.5-129.5-119.4-141C347 36.5 300.6 51.4 268 84L256 96 244 84c-32.6-32.6-79-47.5-124.6-39.9C50.5 55.6 0 115.2 0 185.1v5.8c0 41.5 17.2 81.2 47.6 109.5z"/></svg>
                <?php if (isset($_SESSION["id"])) { if ($info["user_id"] != $_SESSION["id"]) { if ($action == "like") { ?>
                    <a href="/challenges/v/<?php echo $id; ?>?action=like">
                        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><path d="M225.8 468.2l-2.5-2.3L48.1 303.2C17.4 274.7 0 234.7 0 192.8v-3.3c0-70.4 50-130.8 119.2-144C158.6 37.9 198.9 47 231 69.6c9 6.4 17.4 13.8 25 22.3c4.2-4.8 8.7-9.2 13.5-13.3c3.7-3.2 7.5-6.2 11.5-9c0 0 0 0 0 0C313.1 47 353.4 37.9 392.8 45.4C462 58.6 512 119.1 512 189.5v3.3c0 41.9-17.4 81.9-48.1 110.4L288.7 465.9l-2.5 2.3c-8.2 7.6-19 11.9-30.2 11.9s-22-4.2-30.2-11.9zM239.1 145c-.4-.3-.7-.7-1-1.1l-17.8-20c0 0-.1-.1-.1-.1c0 0 0 0 0 0c-23.1-25.9-58-37.7-92-31.2C81.6 101.5 48 142.1 48 189.5v3.3c0 28.5 11.9 55.8 32.8 75.2L256 430.7 431.2 268c20.9-19.4 32.8-46.7 32.8-75.2v-3.3c0-47.3-33.6-88-80.1-96.9c-34-6.5-69 5.4-92 31.2c0 0 0 0-.1 .1s0 0-.1 .1l-17.8 20c-.3 .4-.7 .7-1 1.1c-4.5 4.5-10.6 7-16.9 7s-12.4-2.5-16.9-7z"/></svg>
                    </a>
                    <?php } else { ?>
                    <a href="/challenges/v/<?php echo $id; ?>?action=dislike">
                        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><path d="M47.6 300.4L228.3 469.1c7.5 7 17.4 10.9 27.7 10.9s20.2-3.9 27.7-10.9L464.4 300.4c30.4-28.3 47.6-68 47.6-109.5v-5.8c0-69.9-50.5-129.5-119.4-141C347 36.5 300.6 51.4 268 84L256 96 244 84c-32.6-32.6-79-47.5-124.6-39.9C50.5 55.6 0 115.2 0 185.1v5.8c0 41.5 17.2 81.2 47.6 109.5z"/></svg>
                    </a>
                <?php } } } else { ?>
                    <a href="/login/?login=required">
                        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><path d="M225.8 468.2l-2.5-2.3L48.1 303.2C17.4 274.7 0 234.7 0 192.8v-3.3c0-70.4 50-130.8 119.2-144C158.6 37.9 198.9 47 231 69.6c9 6.4 17.4 13.8 25 22.3c4.2-4.8 8.7-9.2 13.5-13.3c3.7-3.2 7.5-6.2 11.5-9c0 0 0 0 0 0C313.1 47 353.4 37.9 392.8 45.4C462 58.6 512 119.1 512 189.5v3.3c0 41.9-17.4 81.9-48.1 110.4L288.7 465.9l-2.5 2.3c-8.2 7.6-19 11.9-30.2 11.9s-22-4.2-30.2-11.9zM239.1 145c-.4-.3-.7-.7-1-1.1l-17.8-20c0 0-.1-.1-.1-.1c0 0 0 0 0 0c-23.1-25.9-58-37.7-92-31.2C81.6 101.5 48 142.1 48 189.5v3.3c0 28.5 11.9 55.8 32.8 75.2L256 430.7 431.2 268c20.9-19.4 32.8-46.7 32.8-75.2v-3.3c0-47.3-33.6-88-80.1-96.9c-34-6.5-69 5.4-92 31.2c0 0 0 0-.1 .1s0 0-.1 .1l-17.8 20c-.3 .4-.7 .7-1 1.1c-4.5 4.5-10.6 7-16.9 7s-12.4-2.5-16.9-7z"/></svg>
                    <a>
                <?php } ?>
                <?php if ($next["id"]) { ?> 
                    <a href="/challenges/v/<?php echo $next["id"]; ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><path d="M470.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L402.7 256 265.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160zm-352 160l160-160c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L210.7 256 73.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0z"/></svg>
                    </a>    
                <?php } else { ?>
                    <a href="/challenges/add/">
                        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32V224H48c-17.7 0-32 14.3-32 32s14.3 32 32 32H192V432c0 17.7 14.3 32 32 32s32-14.3 32-32V288H400c17.7 0 32-14.3 32-32s-14.3-32-32-32H256V80z"/></svg>
                    </a>
                <?php } ?>
            </div>
        </div>
    </nav>
  </body>
</html>