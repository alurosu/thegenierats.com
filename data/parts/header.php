<header>
    <div>
        <a href="https://thegenierats.com/" alt="thegenierats.com" title="Gamify the Real World" id="logo">
            <h1>The Genie Rats</h1>
        </a>
        <nav>
            <?php if (isset($_SESSION["user"])) { ?>
                <div class="account">
                    <a href="/u/<?php echo $_SESSION["id"]; ?>"><?php echo $_SESSION["user"]; ?></a>
                    <a href="" class="coins"><span><?php echo $_SESSION["coins"]; ?></span> <img src="/data/images/site/cheese.png" alt="Cheese" title="Cheese"></a>
                </div>
            <?php } else { ?>
                <a href="/login" class="account">Login</a>
            <?php } ?>

            <img src="/data/images/site/bars-solid.svg" alt="Bars" class="menu-closed">
            <img src="/data/images/site/x-solid.svg" alt="Bars" class="menu-open">

            <div class="mobile-menu">
                <a href="https://www.redbubble.com/people/thegenierats/shop" rel="nofollow">Shop</a>
                <a href="/challenges">Challenges</a>
                
                <a href="/discord" target="_blank">
                    <img src="/data/images/site/discord.svg" alt="Discord" class="discord">
                    <span>Discord</span>
                </a>
                <a href="/x" target="_blank">
                    <img src="/data/images/site/x.svg" alt="X" class="x">
                    <span>TheGenieRats</span>
                </a>
            </div>
        </nav>
    </div>
</header>