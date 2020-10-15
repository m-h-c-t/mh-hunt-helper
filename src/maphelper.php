<?php
    $title = "MHCT Map Helper";
//    $css = "styles/maphelper.css";
    $js = "scripts/maphelper.js";
    require_once "new-common-header.php";


    require_once "searchByMouse.php";

    $name = "mice";
    echo '<div class="container-fluid" id="results">';
    if (isset($_REQUEST['mice']) && !empty($_REQUEST['mice'])) {
        require_once "helper-results-header.php";
    }

    if (isset($results['results']) && !empty($results['results'])) {
        $column_titles = ["Attraction rate", "Attracted Hunts", "Total Hunts"];
        $result_column_names = ["name" => "", "rate" => "&#37;", "attracted_hunts" => "", "total_hunts" => ""];
        foreach ($results['results'] as $location_id => $result) {
          require "helper-results-body.php";
        }
    }
    echo '</div>';

    require_once "helper-form.php";

    require_once "new-common-footer.php";
