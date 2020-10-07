<?php
    $title = "MHCT Map Helper";
    //$css = "styles/attraction.css";
    //$js = "scripts/maphelper.js";
    require_once "new-common-header.php";
$_REQUEST['mice'] = "white";
    require_once "searchByMouse.php";
echo $results;
    require_once "maphelper-results-header.php";

    include "maphelper-results-body.php";

    require_once "maphelper-mice-form.php";

    require_once "new-common-footer.php";
