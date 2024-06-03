<?php 
session_start();

$result = [];
if (isset($_POST["rat"])) {
    $result["rat"]["id"] = $_POST["rat"];

    if (isset($_SESSION["user"])) {
        $result["trainer"]["id"] = $_SESSION["id"];
        $result["action"]["feed"] = 1;
        if (is_numeric($_POST["feed"]) && $_POST["feed"]>0)
            $result["action"]["feed"] = $_POST["feed"];

        include "config.php";
        // Create a new MySQLi object
        $mysqli = new mysqli($hostname, $username, $password, $database);

        // Check for connection errors
        if ($mysqli->connect_error) {
            $result["error"] = "Connection failed: " . $mysqli->connect_error;
        }

        // check if has enough coins
        $stmt = $mysqli->prepare("SELECT u.coins AS coins, r.xp AS xp, r.lvl AS lvl FROM users u, rats r WHERE u.id = r.user_id AND u.id = ?");
        $stmt->bind_param("i", $result["trainer"]["id"]);
        $stmt->execute();
        $r = $stmt->get_result();
        $i = $r->fetch_assoc();
        $result["trainer"]["coins"] = $i["coins"];
        $result["rat"]["xp"] = $i["xp"];
        $result["rat"]["lvl"] = $i["lvl"];

        if ($i["coins"] >= $result["action"]["feed"]) {
            $result["trainer"]["coins"] = $i["coins"]-$result["action"]["feed"];
            $result["rat"]["xp"] += $result["action"]["feed"]*$xp['coin2xp'];
            $result["rat"]["xpdelta"] = $result["action"]["feed"]*$xp['coin2xp'];

            // level up
            while (floor($xp['base'] * pow($result["rat"]["lvl"], $xp['e'])) <= $result["rat"]["xp"]) {
                $result["rat"]["lvl"]++;
            }

            // calculate xp %
            $percentage = ($result["rat"]["xp"] - $xp['base'] * pow($result["rat"]["lvl"]-1, $xp['e'])) / ($xp['base'] * (pow($result["rat"]["lvl"], $xp['e']) - pow($result["rat"]["lvl"]-1, $xp['e']))) * 100;
            $result["rat"]["xppercent"] = $percentage;

            // update db
            $stmt = $mysqli->prepare("UPDATE users SET coins = ? WHERE id = ?");
            $stmt->bind_param("ii", $result["trainer"]["coins"], $result["trainer"]["id"]);
            $stmt->execute();
            $stmt = $mysqli->prepare("UPDATE rats SET xp = ? , lvl = ? WHERE id = ?");
            $stmt->bind_param("iii", $result["rat"]["xp"], $result["rat"]["lvl"], $result["rat"]["id"]);
            $stmt->execute();

            $result["success"] = "You gave ".$result["action"]["feed"]." coins to Rat #".sprintf('%03d', $result["rat"]["id"]);
        } else $result["error"] = "Not enough cheese.";

        // Close the statement and database connection
        $stmt->close();
        $mysqli->close();
    } else $result["error"] = "Please login and try again.";
} else $result["error"] = "Which [rat] you want to feed?";

echo json_encode($result);
?>