<html lang="en">
<head>
    <title>MHCT Tracker</title>
    <?php require "common_head.php"; ?>
</head>
<body style="text-align: center;" class="text-center">
    <!-- Jumbotron -->
    <div class="jumbotron">
        <h1>MHCT Tracker</h1>
        <a href="/" class="clickable"><span class="glyphicon glyphicon-chevron-left"></span> MHCT Tools</a>
    </div>
    <div class="container">
        <?php
        $file_name = 'tracker.json';
        $location = 'Unknown';
        $time_since = 'a while';
        $data = file_get_contents($file_name);

        if (!empty($data)) {
            $data = json_decode($data);

            if (!empty($data->rh->location)) {
                $location = $data->rh->location;
            }

            if ($data->rh->last_seen != 0) {
                $last_seen = $data->rh->last_seen;
                $date = new DateTime("@$last_seen");
                $now = new DateTime();

                $time_since = $date->diff($now)->format("%h hour(s) and %i minute(s)");
            }
        }

        ?>
        <h3>Current Relic Hunter Location: <?php print $location; ?>.<br/>
            <small>Last seen (after last move): <?php print $time_since; ?> ago.</small>
        </h3><br/><br/>

        <?php require_once "stats.php"; ?><br/><br/>
        <?php //require_once "missing_mice.php"; ?>

        <br/><p class="text-center">For more info, copy of the data, or if you want to help with data gathering, please look <a href="/">here</a>.</p>
    </div>
    <?php require "common_footer.php"; ?>
</body>
</html>
