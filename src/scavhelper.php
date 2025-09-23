<?php
    $title = "MHCT Scavenger Helper";
//    $css = "styles/maphelper.css";
    $fluid_container = true;
    $js = "scripts/scavhelper.js";

    $load_tablesorter_libraries = true;
    require_once "common-header.php";


    require_once "searchByDrop.php";

    $name = "items";
    echo '<div class="container-fluid" id="results">';
    if (isset($_REQUEST['items']) && !empty($_REQUEST['items'])) {
        require_once "helper-results-header.php";
    }

    if (isset($results['results']) && !empty($results['results'])) {
        $column_titles = ["Drop rate", "Total Catches", "Total Catches"];
        $result_column_names = ["name" => "", "drop_rate" => "&#37;", "total_catches" => "", "total_hunts" => ""];
        foreach ($results['results'] as $location_id => $result) {
          require "helper-results-body.php";
        }
    }
    echo '</div>';

    if (isset($results['results']) && !empty($results['results'])) {
        require "helper-results-grid-scav.php";
    }

    require_once "helper-form.php";

    require_once "common-footer.php";
