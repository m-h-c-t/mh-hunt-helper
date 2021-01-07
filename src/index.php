<!DOCTYPE html>
<html lang="en">
<head>
    <?php require "common_head.php"; ?>

    <title>MHCT Tools</title>
    <link rel="stylesheet" type="text/css" href="styles/main.css">
</head>
<body class="text-center">
<!-- Jumbotron -->
<div class="jumbotron">
    <h1>MouseHunt Community Tools</h1>
</div>
<div class="container">
    <div class="col-md-6 col-md-offset-3">
        <table class="table text-center table-responsive table-hover table-bordered">
            <thead>
            <!-- <h3>MouseHunt Tools <span class="glyphicon glyphicon-flag"></span></h3> -->
            </thead>
            <tbody>
            <tr><td>
                <a tabindex="0" class="glyphicon glyphicon-question-sign pull-right popover_styles" role="button" data-toggle="popover"
                    data-content="Copy/Paste (OR Automatically filled by extension below) a list of your map mice into this tool,
                    and it will show you locations with stages, cheese, attraction rates, and mice in each area, sorted by descending
                    number of mice in each location."></a>
                <a href="/maphelper.php" style="display:block;text-decoration:none;color:#333;">Map Helper</a>
            </td></tr>
            <tr><td>
                <a tabindex="0" class="glyphicon glyphicon-question-sign pull-right popover_styles" role="button" data-toggle="popover"
                    data-content="Copy/Paste a list of your scavenger map items into this tool,
                    and it will show you locations with stages, cheese, drop rates, and items in each area, sorted by descending
                    number of items in each location."></a>
                <a href="/scavhelper.php" style="display:block;text-decoration:none;color:#333;">Scavenger Helper</a>
            </td></tr>
            <tr><td>
                <a tabindex="0" class="glyphicon glyphicon-question-sign pull-right popover_styles" role="button" data-toggle="popover"
                    data-content="Search by mouse name to see the location, cheese and attraction rate of a specific mouse."></a>
                <a href="/attractions.php" style="display:block;text-decoration:none;color:#333;">Attraction Rate</a>
            </td></tr>
            <tr><td>
                <a tabindex="0" class="glyphicon glyphicon-question-sign pull-right popover_styles" role="button" data-toggle="popover"
                    data-content="Search by loot name to see the location and drop rate of a specific loot."></a>
                <a href="/loot.php" style="display:block;text-decoration:none;color:#333;">Looter</a>
            </td></tr>
            <tr><td>
                <a tabindex="0" class="glyphicon glyphicon-question-sign pull-right popover_styles" role="button" data-toggle="popover"
                    data-content="Search maps to see what mice are likely to appear on them."></a>
                <a href="/mapper.php" style="display:block;text-decoration:none;color:#333;">Mapper</a>
            </td></tr>
            <tr><td>
                <a tabindex="0" class="glyphicon glyphicon-question-sign pull-right popover_styles" role="button" data-toggle="popover"
                    data-content="Search convertibles like chests, to see what items are likely to be in them."></a>
                <a href="/converter.php" style="display:block;text-decoration:none;color:#333;">Converter</a>
            </td></tr>
            <tr><td>
                <a tabindex="0" class="glyphicon glyphicon-question-sign pull-right popover_styles" role="button" data-toggle="popover"
                    data-content="See stats about these tools as well as confirmed RH location. (RH must be caught by one of us for it to show up here)"></a>
                <a href="/tracker.php" style="display:block;text-decoration:none;color:#333;">Tracker</a>
            </td></tr>
            <tr><td>
                <a href="/faq.php" style="display:block;text-decoration:none;color:#333;">F.A.Q.</a>
            </td></tr>
            </tbody>
        </table>
    </div>
</div>
<div class="container">
    <p class="muted">
        If you'd like to contribute, please install the browser extension below and consider supporting us on <a href="https://www.patreon.com/mhct" target="_blank">Patreon</a>!
        <br/>Our code is open source on <a href="https://github.com/mh-community-tools" target="_blank">GitHub</a> and SQL database dumps can be downloaded from <a href="https://keybase.pub/devjacksmith/mh_backups/" target="_blank">Keybase</a> or <a href="https://hub.docker.com/r/tsitu/mhct-db-docker" target="_blank">Docker</a>.
        <br/>Also check out <a href="https://www.reddit.com/r/mousehunt/" target="_blank">/r/mousehunt</a> on Reddit and join the conversation on <a href="https://discord.gg/E4utmBD" target="_blank">Discord</a>.
        <br/>Maintained by the MHCT team. <b>Thank you</b> to all of our contributors!
    </p>
</div>
<div class="container">
    <div class="text-center">
        <a href="https://chrome.google.com/webstore/detail/mhct-mousehunt-helper/ghfmjkamilolkalibpmokjigalmncfek?authuser=1" target="_blank" style="display:inline-block;text-decoration:none;color:#333;"><img src="images/chrome.png"></a>
        <a href="https://addons.mozilla.org/en-US/firefox/addon/mhct-mousehunt-helper/" target="_blank" style="display:inline-block;text-decoration:none;color:#333;"><img src="images/firefox.png"><b> Add-on</b></a>
        <br/><a href="https://www.patreon.com/mhct" target="_blank"><img src="images/patreon.png" style="width:150px;margin-top:10px;"></a>
    </div>
</div>
<br/>
<?php require "common_footer.php"; ?>
<script defer type="text/javascript" src="scripts/main.js"></script>
</body>
</html>
