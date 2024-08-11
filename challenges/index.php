<?php 
session_start();
include "../data/parts/config.php";
// Create a new MySQLi object
$mysqli = new mysqli($hostname, $username, $password, $database);

// Check for connection errors
if ($mysqli->connect_error) {
  $error = "Connection failed: " . $mysqli->connect_error;
}
// Prepare the SQL statement
$time = time();
$stmt = $mysqli->prepare("SELECT id, title, ends_at FROM quests WHERE ends_at > ".$time." ORDER BY id ASC LIMIT 1");

// Bind parameters and execute the statement
$stmt->execute();

// Check if the statement was executed successfully
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$quest_id = $row["id"];
$title = $row["title"];
$ends_at = $row["ends_at"];

// get entries
$stmt = $mysqli->prepare("SELECT qe.id AS id, qe.title AS title, qe.link AS link, u.id AS user_id, u.user AS user, u.avatar AS avatar, qe.votes AS votes FROM quest_entries qe, users u WHERE qe.quest_id=? AND qe.user_id = u.id ORDER BY votes DESC LIMIT 100");
$stmt->bind_param("i", $quest_id);
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
    <title>Explore Challenges - The Genie Rats</title>
    <meta name="description" content="Discover and conquer a variety of thrilling challenges. Earn cheese and redefine excitement in our vibrant colony">

    <?php include '../data/parts/includes.php'; ?>    
  </head>
  <body>
    <?php include '../data/parts/header.php'; ?>
    <main id="challenges">
        <article id="challenge-summary">
            <img src="/data/images/site/challenge_active.png" alt="TheGenieRats Challenge" title="Are you up for the task?">
            <div>
                <p>Active challenge:</p>
                <h2><?php echo $title; ?></h2>
                <div class="form-footer">
                    <a href="/about/#how-to">How it works</a>
                    <a href="/challenges/add" id="add-challenge-button">
                        <button>Enter</button>
                    </a>
                </div>
            </div>
        </article>
        <div id="countdown-wrapper">
            <div id="countdown" data-end="<?php echo $ends_at; ?>">
                <ul>
                    <li id="days"><span>&nbsp;</span></li>
                    <li id="hours"></li>
                    <li id="minutes"></li>
                    <li id="seconds"></li>
                </ul>
            </div>
        </div>
        <div id="user-entries">
            <?php if (!$quest_entries) { ?>
                <p class="details">
                    No user has participated yet. Be the first to enter!
                </p>
            <?php } else { ?>
                <h2>Most Upvoted Entries</h2>
                <p class="details">(click play to see what others did)</p>
                <?php 
                $i=0;
                foreach ($quest_entries as $entry) {
                    $i++;
                ?>
                    <div>
                        <div class="rank">
                            <div>#<?php echo $i;?></div>
                            Rank
                        </div>
                        <a href="/challenges/v/<?php echo $entry["id"];?>" class="video challenge-watch-image">
                            <img src="<?php echo $entry["avatar"]; ?>" alt="<?php echo $entry["user"];?>">
                        </a>
                        <div class="title">
                            <span>
                                <?php echo $entry["title"]; ?>
                            </span>
                            <div class="votes">
                                Votes: <?php echo $entry["votes"]; ?>
                            </div>
                            <div class="user">
                                <a href="/u/<?php echo $entry["user_id"];?>" target="_blank"><?php echo $entry["user"];?></a>
                            </div>
                        </div>
                        <div>
                            <a href="/challenges/v/<?php echo $entry["id"];?>" class="challenge-watch-button">
                                <button>Watch Video</button>
                            </a>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </main>
    <?php include '../data/parts/footer.php'; ?>
  </body>
</html>