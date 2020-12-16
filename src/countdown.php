<?php
    $title = "MHCT Event Countdown";
//    $css = "styles/maphelper.css";
    $js = "scripts/countdown.js";
    require_once "new-common-header.php";
    require_once "countdown-settings.php";

    echo '<h1 id="eventname">' . $eventName . '</h1>';
    echo '<h3 id="countdown"></h3>';
    echo '<input id="countdownDate" type="hidden" value="' . $countdownDate . '">';
    echo '<h4 id="countdownDisplayDate">That is ' . $countdownDate . ' UTC</h3>';
    echo '<br/>Special thanks to <a href="https://github.com/chirpphixel/chirpphixel.github.io" target="_blank">chirpphixel</a>
    and <a href="https://sites.google.com/site/mhtimerslinks/" target="_blank">Chad\'s Timers page</a> for inspiration.<br/>';

    require_once "new-common-footer.php";
?>
