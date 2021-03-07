<?php
    $title = "MHCT Converter";
    // $css = "styles/loot.css";
    $js = "scripts/converter.js";
    require_once "new-common-header.php";
?>

        <input id="item_type" type="hidden" value="convertible">
        <?php

        if (!empty($_GET['item'])) {
            print '<input id="prev_item" type="hidden" value="' . $_GET['item'] . '">';
        } ?>

        <div class="input-group col-sm-6 col-sm-offset-3">
            <div class="input-group-addon"><strong>Convertible:</strong></div>
            <input name="item" id="item" class="form-control input-lg" type="text" placeholder="Start typing convertible name and select." autofocus>
            <div id="erase_item" class="input-group-addon fakebutton"><span class="glyphicon glyphicon-remove"></span></div>
        </div>

        <div id="results_total"></div>
        <div id="results" class="table-responsive"></div>


<?php require "new-common-footer.php"; ?>
