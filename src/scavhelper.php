<?php
    $title = "MHCT Scavenger Helper";
//    $css = "styles/maphelper.css";
    $js = "scripts/scavhelper.js";
    require_once "new-common-header.php";


    require_once "searchByDrop.php";

    echo '<div class="container-fluid" id="results">';
    if (isset($_REQUEST['items']) && !empty($_REQUEST['items'])) {
        require_once "scavhelper-results-header.php";
    }

    if (isset($results['results']) && !empty($results['results'])) {
      foreach ($results['results'] as $location_id => $result) {
        require "scavhelper-results-body.php";
      }
    }
    echo '</div>';

    require_once "scavhelper-items-form.php";

    require_once "new-common-footer.php";
