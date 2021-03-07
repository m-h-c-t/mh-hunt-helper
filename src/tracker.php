<?php
    $title = "MHCT Tracker";
    // $css = "styles/loot.css";
    // $js = "scripts/attraction.js";
    require_once "new-common-header.php";
?>

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

        <?php require_once "stats.php"; ?><br/>
        <?php //require_once "missing_mice.php"; ?>


<?php require_once "new-common-footer.php"; ?>
