<?php
    $title = "MHCT Event Countdown";
//    $css = "styles/maphelper.css";
    $js = "scripts/countdown.js";
    require_once "new-common-header.php";
    require_once "countdown-settings.php";

    echo '<h1 id="eventname">' . $eventName . '</h1>';
    echo '<h3 id="countdown"></h3>';
    echo '<input id="countdownDate" type="hidden" value="' . $countdownDate . '">';

    require_once "new-common-footer.php";
?>
