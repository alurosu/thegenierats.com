<?php 
session_start();
include "../data/parts/config.php";
// Create a new MySQLi object
$mysqli = new mysqli($hostname, $username, $password, $database);

// Check for connection errors
if ($mysqli->connect_error) {
  $error = "Connection failed: " . $mysqli->connect_error;
}

// get entries
$stmt = $mysqli->prepare("SELECT r.id AS id, u.user AS user, r.lvl AS lvl, r.xp AS xp FROM rats r, users u WHERE r.user_id=u.id ORDER BY r.lvl DESC, r.xp DESC LIMIT 100");
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
    <title>Leaderboard - The Genie Rats</title>
    <meta name="description" content="See where you stand among fellow adventurers. Check the leaderboard, track your progress, and strive to climb the ranks in our vibrant colony of challenge conquerors.">

    <?php include '../data/parts/includes.php'; ?>    
  </head>
  <body>
    <?php include '../data/parts/header.php'; ?>
    <main id="challenges">
        <div id="user-entries">
            <?php if (!$quest_entries) { ?>
                <p class="details">
                    We can't find any rat.
                </p>
            <?php } else { ?>
                <h2 class="mb-5">Rats Hall of Fame</h2>
                <?php 
                $i=0;
                foreach ($quest_entries as $entry) {
                    $i++;
                ?>
                    <div class="d-flex align-items-center justify-content-center">
                        <div class="rank">
                            <div>#<?php echo $i;?></div>
                            Rank
                        </div>
                        <img class="w-auto px-2" style="max-height: 6em;" src="/data/images/site/rats/<?php echo ($entry["lvl"] < 10) ? $entry["lvl"] : 10; ?>.gif" alt="Rat #<?php echo sprintf('%03d', $entry["id"]); ?> icon">
                        <div class="">
                          <span>
                              Rat #<?php echo sprintf('%03d', $entry["id"]); ?>
                          </span>
                          <div class="level">
                            <span>Level <?php echo $entry["lvl"]; ?></span>
                            <?php 
                            $percentage = ($entry["xp"] - $xp['base'] * pow($entry["lvl"]-1, $xp['e'])) / ($xp['base'] * (pow($entry["lvl"], $xp['e']) - pow($entry["lvl"]-1, $xp['e']))) * 100;
                            ?>
                            <div class="bar" style="width: <?php echo $percentage;?>%"></div>
                          </div>
                          <div class="user pt-2 fs-6">
                              <a href="/u/<?php echo $entry["user_id"];?>" target="_blank"><?php echo $entry["user"];?></a>
                          </div>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </main>
    <?php include '../data/parts/footer.php'; ?>
  </body>
</html>