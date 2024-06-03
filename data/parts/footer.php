<footer>
    <ul>
        <li>
            <a href="/x" target="_blank">Contact</a>
        </li>
        <li>
            <a href="/discord" target="_blank">Discord</a>
        </li>
        <li>
            <a href="" target="_blank">OpenSea</a>
        </li>
    </ul>
</footer>

<?php if ($error) { ?>
    <div class="form-errors alert-box"><?php echo $error; ?> <span>x</span></div>
<?php } ?>
<?php if ($notice) { ?>
    <div class="alert-box"><?php echo $notice; ?> <span>x</span></div>
<?php } ?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script src="/data/main.js?v=15"></script>