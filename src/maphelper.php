<?php
    $title = "MHCT Map Helper";
//    $css = "styles/maphelper.css";
    $js = "scripts/maphelper.js";
    require_once "new-common-header.php";


    require_once "searchByMouse.php";

    echo '<div class="container-fluid" id="results">';
    if ($_REQUEST['mice']) {
        require_once "maphelper-results-header.php";
    }

    if ($results['results']) {
      foreach ($results['results'] as $location_id => $result) {
        require "maphelper-results-body.php";
      }
    }
    echo '</div>';

    require_once "maphelper-mice-form.php";

    require_once "new-common-footer.php";
