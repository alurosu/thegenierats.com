<?php 
session_start();

$id=1;
if (is_numeric($_GET["id"]) && $_GET["id"]>0)
  $id = $_GET["id"];

include "../data/parts/config.php";
// Create a new MySQLi object
$mysqli = new mysqli($hostname, $username, $password, $database);

// Check for connection errors
if ($mysqli->connect_error) {
  $error = "Connection failed: " . $mysqli->connect_error;
}

// Prepare the SQL statement
$stmt = $mysqli->prepare("SELECT user, avatar, challenges, votes, god, coins FROM users WHERE id = ?");

// Bind parameters and execute the statement
$stmt->bind_param("i", $id);
$stmt->execute();

// Check if the statement was executed successfully
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$user = $row["user"];
$challenges = $row["challenges"];
$votes = $row["votes"];
$avatar = $row["avatar"];
$god = $row["god"];
if (isset($_SESSION["user"]) && $_SESSION["user"] == $user)
  $_SESSION["coins"] = $row["coins"];

// get rats
$stmt = $mysqli->prepare("SELECT id, lvl ,xp FROM rats WHERE user_id = ? ORDER BY lvl DESC LIMIT 100");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$rats = [];
while ($rat=$result->fetch_assoc())
    $rats[] = $rat;

// get entries
$stmt = $mysqli->prepare("SELECT qe.id AS id, qe.votes AS votes, qe.title AS title, qe.link AS link, u.id AS user_id, u.user AS user FROM quest_entries qe, users u WHERE qe.user_id = ? AND qe.user_id = u.id ORDER BY id DESC LIMIT 100");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$quest_entries = [];
while ($row=$result->fetch_assoc())
    $quest_entries[] = $row;

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
    <title><?php echo $user;?> - The Genie Rats</title>
    <link rel="stylesheet" href="/data/main.css?v=101">
    <link rel="icon" href="/data/thegenierats.ico" type="image/x-icon">
  </head>
  <body>
    <?php include '../data/parts/header.php'; ?>
    <main class="user-page">
        <div class="profile-name">
            <img src="<?php echo $avatar; ?>" alt="<?php echo $user;?>'s avatar" title="<?php echo $user;?>'s avatar">
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
        <?php if (isset($_SESSION["user"]) && $_SESSION["user"] == $user) { ?>
          <div class="profile-menu">
              <h2>welcome back</h2>
              <ul>
                  <li>
                    <a href="/settings">
                      <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><path d="M495.9 166.6c3.2 8.7 .5 18.4-6.4 24.6l-43.3 39.4c1.1 8.3 1.7 16.8 1.7 25.4s-.6 17.1-1.7 25.4l43.3 39.4c6.9 6.2 9.6 15.9 6.4 24.6c-4.4 11.9-9.7 23.3-15.8 34.3l-4.7 8.1c-6.6 11-14 21.4-22.1 31.2c-5.9 7.2-15.7 9.6-24.5 6.8l-55.7-17.7c-13.4 10.3-28.2 18.9-44 25.4l-12.5 57.1c-2 9.1-9 16.3-18.2 17.8c-13.8 2.3-28 3.5-42.5 3.5s-28.7-1.2-42.5-3.5c-9.2-1.5-16.2-8.7-18.2-17.8l-12.5-57.1c-15.8-6.5-30.6-15.1-44-25.4L83.1 425.9c-8.8 2.8-18.6 .3-24.5-6.8c-8.1-9.8-15.5-20.2-22.1-31.2l-4.7-8.1c-6.1-11-11.4-22.4-15.8-34.3c-3.2-8.7-.5-18.4 6.4-24.6l43.3-39.4C64.6 273.1 64 264.6 64 256s.6-17.1 1.7-25.4L22.4 191.2c-6.9-6.2-9.6-15.9-6.4-24.6c4.4-11.9 9.7-23.3 15.8-34.3l4.7-8.1c6.6-11 14-21.4 22.1-31.2c5.9-7.2 15.7-9.6 24.5-6.8l55.7 17.7c13.4-10.3 28.2-18.9 44-25.4l12.5-57.1c2-9.1 9-16.3 18.2-17.8C227.3 1.2 241.5 0 256 0s28.7 1.2 42.5 3.5c9.2 1.5 16.2 8.7 18.2 17.8l12.5 57.1c15.8 6.5 30.6 15.1 44 25.4l55.7-17.7c8.8-2.8 18.6-.3 24.5 6.8c8.1 9.8 15.5 20.2 22.1 31.2l4.7 8.1c6.1 11 11.4 22.4 15.8 34.3zM256 336a80 80 0 1 0 0-160 80 80 0 1 0 0 160z"/></svg>
                    </a>
                  </li>
                  <li>
                      <a href="/login?action=logout">Logout</a>
                  </li>
              </ul>
          </div>
          <form action="" method="post" enctype="multipart/form-data" style="display:none;" id="change-avatar">
            <input type="file" name="fileToUpload" id="fileToUpload"><input type="submit" value="Upload Image" name="submit">
          </form>
        <?php } ?>
        <div class="profile">
          <svg viewBox="0 0 200 200" version="1.1" xmlns="http://www.w3.org/2000/svg">
            <title>Rat #<?php echo sprintf('%03d', $rats[0]["id"]);?> Photo</title>
            <defs>
              <pattern id="img" patternUnits="userSpaceOnUse" width="200" height="200">
              <image xlink:href="/data/images/site/forgot_password.png" x="10" y="10" width="180" height="180" preserveAspectRatio="xMinYMin slice"/>
              </pattern>
            </defs>
            <polygon class="outer-border" points="136.737609507049,188.692435121084 63.2623904929514,188.692435121084 11.3075648789165,136.737609507049 11.3075648789165,63.2623904929514 63.2623904929513,11.3075648789165 136.737609507049,11.3075648789165 188.692435121084,63.2623904929513 188.692435121084,136.737609507049" fill="url(#img)"/>
            <polygon class="inner-border" points="136.737609507049,188.692435121084 63.2623904929514,188.692435121084 11.3075648789165,136.737609507049 11.3075648789165,63.2623904929514 63.2623904929513,11.3075648789165 136.737609507049,11.3075648789165 188.692435121084,63.2623904929513 188.692435121084,136.737609507049" fill="transparent"/>
          </svg>
          <div class="profile-info">
            <div class="profile-stats">
              <h3>Rat #<?php echo sprintf('%03d', $rats[0]["id"]); ?></h3>
              <div class="level">
                <span>Level <?php echo $rats[0]["lvl"]; ?></span>
                <?php 
                $percentage = ($rats[0]["xp"] - $xp['base'] * pow($rats[0]["lvl"]-1, $xp['e'])) / ($xp['base'] * (pow($rats[0]["lvl"], $xp['e']) - pow($rats[0]["lvl"]-1, $xp['e']))) * 100;
                ?>
                <div class="bar" style="width: <?php echo $percentage;?>%"></div>
              </div>
            </div>
            <?php if (isset($_SESSION["user"]) && $_SESSION["user"] == $user) { ?>
              <div id="feed">
                <input type="number" min ="1" data-id="<?php echo $rats[0]["id"]; ?>" id="feed-amount" value="1" placeholder="1,2..">
                <div id="feed-rat">
                  Feed <img src="/data/images/site/cheese.png" alt="Cheese" title="Cheese">
                </div>
              </div>
            <?php } ?>
          </div>
        </div>
        <div id="user-entries">
            <?php if (!$quest_entries) { ?>
                <p class="details">
                  Hasn't participated in any challenges yet.
                </p>
            <?php } else { ?>
                <h2>Recent Challenges</h2>
                <p class="details">(click play for video and details)</p>
                <?php 
                foreach ($quest_entries as $entry) {
                ?>
                    <div>
                        <div class="title">
                            <span>
                                <?php echo $entry["title"]; ?>
                            </span>
                            <div class="votes">
                                Votes: <?php echo $entry["votes"]; ?>
                            </div>
                        </div>
                        <a href="/challenges/v/<?php echo $entry["id"];?>" class="video" title="Watch Video">
                            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><path d="M464 256A208 208 0 1 0 48 256a208 208 0 1 0 416 0zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256zM188.3 147.1c7.6-4.2 16.8-4.1 24.3 .5l144 88c7.1 4.4 11.5 12.1 11.5 20.5s-4.4 16.1-11.5 20.5l-144 88c-7.4 4.5-16.7 4.7-24.3 .5s-12.3-12.2-12.3-20.9V168c0-8.7 4.7-16.7 12.3-20.9z"/></svg>
                        </a>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </main>
    <?php include '../data/parts/footer.php'; ?>
  </body>
</html>