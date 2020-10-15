<?php
    $title = "MHCT Scavenger Helper";
//    $css = "styles/maphelper.css";
    $js = "scripts/scavhelper.js";
    require_once "new-common-header.php";


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

    require_once "helper-form.php";

    require_once "new-common-footer.php";
