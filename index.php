<?php 
session_start();
include "data/parts/config.php";
// Create a new MySQLi object
$mysqli = new mysqli($hostname, $username, $password, $database);

// Check for connection errors
if ($mysqli->connect_error) {
  $error = "Connection failed: " . $mysqli->connect_error;
}
// Prepare the SQL statement
$stmt = $mysqli->prepare("SELECT title FROM quests ORDER BY id DESC LIMIT 1");

// Bind parameters and execute the statement
$stmt->execute();

// Check if the statement was executed successfully
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$title = $row["title"];

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
    <title>The Genie Rats - Gamify the Real World and Conquer Challenges</title>
    <meta name="description" content="Join our vibrant colony, fight boredom and get cheese to feed your rat. Redefine excitement in a world of thrilling adventures.">
    <meta name="author" content="The Genie Rats">
    <?php include 'data/parts/includes.php'; ?>    
  </head>
  <body>
    <?php include 'data/parts/header.php'; ?>
    <main id="homepage">
        <article id="about">
            <div>
                <img src="/data/images/site/logo.png" alt="TheGenieRats Logo" title="Want to see some magic?">
                <div>
                    <h2>The Genie Rats</h2>
                    <p>An evergrowing team that wants to contribute to our society by digitally gamifying the real world. </p>
                    <h2>Our Mission</h2>
                    <p>We plan to revolutionize the concept of entertainment and community engagement by providing an immersive experience that combines exciting challenges and exclusive NFTs.</p>
                </div>
            </div>
        </article>
        <div id="how-to"></div>
        <article id="roadmap">
            <div>
                <h2>What are challenges?</h2>
                <p>We want to motivate people and have common goals. Let's make ourselves and the world better. To achieve this goal, we put up challenges on our website.</p>
                <h3>How to participate?</h3>
                <p>The process is quite straight forward. Here's how you do it:</p>
                <ul>
                    <li>Make a YouTube short, TikTok or Instagram reel that proves you completed the challenge</li>
                    <li>Login and paste your video link into <a href="/challenges/add">this form</a></li>
                    <li>Earn cheese everytime your video gets likes</li>
                    <li>Give cheese to your rat that grows in level, if you feed it enough</li>
                </ul>
                <p>We recommend using the hashtags <span class="fw-bold text-success">#ratquest</span> or <span class="fw-bold text-success">#thegenierats</span> in your video description.</p>
                <p>more participants = more upvotes</p>
            </div>
            <img src="/data/images/site/roadmap.png" alt="TheGenieRats Roadmap" title="Let's plan this out!">
        </article>
        <div id="community">
            <div>
                <a href="/discord" target="_blank">
                    <img src="/data/images/site/discord.svg" alt="Discord">
                    <p>Connect with like-minded individuals<br/><span>Join our Discord server</span></p>
                </a>
            </div>
        </div>
        <article id="challenge-summary" class="mb-4 pb-4">
            <img src="/data/images/site/challenge_active.png" alt="TheGenieRats Challenge" title="Are you up for the task?">
            <div>
                <p>Active Challenge:</p>
                <h2><?php echo $title; ?></h2>
                <div class="form-footer">
                    <a href="/#how-to" target="_blank">How it works</a>
                    <a href="/challenges"><button>Details</button></a>
                </div>
            </div>
        </article>
        <?php /*
        <article id="roadmap">
            <div>
                <h2>Roadmap</h2>
                <p>This is a rough representation of what we plan to do. <br/>We'll share detailed announcements in the updates section from <a href="/discord" target="_blank">our Discord</a> server.</p>
                <h3>Q3 2023</h3>
                <ul>
                    <li>Launch community Discord server.</li>
                    <li>Launch merch shop for supporting members.</li>
                    <li>Launch website.</li>
                </ul>
                <h3>Q4 2023</h3>
                <ul>
                    <li>Launch generative 10000 unique profile picture NFTs.</li>
                    <li>Add website registration and login using Discord.</li>
                </ul>
                <h3>Q1 2024</h3>
                <ul>
                    <li>Develop mobile app for challenges (Android and iOS).</li>
                    <li>Add option to mint NFTs on multiple blockchains.</li>
                </ul>
            </div>
            <img src="/data/images/site/roadmap.png" alt="TheGenieRats Roadmap" title="Let's plan this out!">
        </article>
        <div id="collection">
            <div>
                <p>Rat Army NFTs</p>
                <h2>Recent Transactions</h2>
                <div class="nfts">
                    <?php 
                    function format_star_price($price) {
                        $price = $price / 1000000;
                        if ($price >= 1000)
                            $price = number_format((float)($price/1000), 1, '.', ''). "k";
                        return  $price;
                    }
                    $nfts = json_decode(file_get_contents("data/parts/nft-latest-transactions.js"));
                    $i=1;
                    foreach ($nfts->data->events->edges as $nft) {
                    ?>
                        <div class="element">
                            <img src="https://ipfs-gw.stargaze-apis.com/ipfs/QmbGvE3wmxex8KiBbbvMjR8f9adR28s3XkiZSTuGmHoMHV/<?php echo $nft->node->data->tokenId;?>.jpg" alt="">
                            <a class="id" href="https://www.stargaze.zone/marketplace/stars19jq6mj84cnt9p7sagjxqf8hxtczwc8wlpuwe4sh62w45aheseues57n420/<?php echo $nft->node->data->tokenId;?>" target="_blank">#<?php echo sprintf('%04d', $nft->node->data->tokenId);?></a>
                            <span class="price"><?php echo format_star_price($nft->node->data->price);?> STAR ($<?php echo $nft->node->data->priceUsd;?>)</span>
                            <span class="date"><?php echo date("d M Y", strtotime($nft->node->createdAt));?></span>
                        </div>
                    <?php
                        $i++;
                    }
                    ?>
                    <div class="sync">
                        <?php
                        function time_elapsed_string($datetime, $full = false) {
                            $now = new DateTime;
                            $ago = new DateTime($datetime);
                            $diff = $now->diff($ago);
                        
                            $diff->w = floor($diff->d / 7);
                            $diff->d -= $diff->w * 7;
                        
                            $string = array(
                                'y' => 'year',
                                'm' => 'month',
                                'w' => 'week',
                                'd' => 'day',
                                'h' => 'hour',
                                'i' => 'minute',
                                's' => 'second',
                            );
                            foreach ($string as $k => &$v) {
                                if ($diff->$k) {
                                    $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
                                } else {
                                    unset($string[$k]);
                                }
                            }
                        
                            if (!$full) $string = array_slice($string, 0, 1);
                            return $string ? implode(', ', $string) . ' ago' : 'just now';
                        }
                        ?>
                        Updated <?php echo time_elapsed_string("@".$nfts->updated);?>
                    </div>
                </div>

                <div class="more">
                    <h3>Join the rat army!</h3>
                    <p>Own an uniquely generated profile picture from our collection.</p>
                    <a href="https://www.stargaze.zone/marketplace/stars19jq6mj84cnt9p7sagjxqf8hxtczwc8wlpuwe4sh62w45aheseues57n420" target="_blank">View All Soldiers</a>
                </div>
            </div>
        </div>
        */ ?>
    </main>
    <?php include 'data/parts/footer.php'; ?>
  </body>
</html>