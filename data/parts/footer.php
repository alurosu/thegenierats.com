<footer>
    <ul>
        <li>
            <a href="/x" target="_blank">Contact</a>
        </li>
        <li>
            <a href="/discord" target="_blank">Discord</a>
        </li>
    </ul>
    <div class="col-12 pt-2 pb-4 fs-6 text-center text-body-secondary">&#169; The Genie Rats <?PHP echo date('Y');?></div>
</footer>

<?php if ($error) { ?>
    <div class="form-errors alert-box"><?php echo $error; ?> <span>x</span></div>
<?php } ?>
<?php if ($notice) { ?>
    <div class="alert-box"><?php echo $notice; ?> <span>x</span></div>
<?php } ?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script src="/data/main.js?v=16"></script>