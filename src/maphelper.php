<?php
    $title = "MHCT Map Helper";
    $css = "styles/maphelper.css";
    //$js = "scripts/maphelper.js";
    require_once "new-common-header.php";
$_REQUEST['mice'] = "white||brown||grey||faketest";
    require_once "searchByMouse.php";
//echo "<pre>".json_encode($results, JSON_PRETTY_PRINT)."<pre>";
    require_once "maphelper-results-header.php";

    foreach ($results['results'] as $location_id => $result) {
        require "maphelper-results-body.php";
    }

    require_once "maphelper-mice-form.php";

    require_once "new-common-footer.php";
