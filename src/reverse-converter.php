<?php
    $title = "MHCT Reverse Converter";
    // $css = "styles/loot.css";
    $js = "scripts/reverse-converter.js";
    $load_datatable_libraries = true;
    require_once "common-header.php";
?>

        <input id="item_type" type="hidden" value="item">
        <?php

        if (!empty($_GET['item'])) {
            print '<input id="prev_item" type="hidden" value="' . $_GET['item'] . '">';
        } ?>

        <div class="input-group col-sm-6 col-sm-offset-3">
            <div class="input-group-addon"><strong>Item:</strong></div>
            <input name="item" id="item" class="form-control input-lg" type="text" placeholder="Start typing item name and select." autofocus>
            <div id="erase_item" class="input-group-addon fakebutton"><span class="glyphicon glyphicon-remove"></span></div>
        </div>

        <div id="results_total"></div>
        <div id="results" class="table-responsive"></div>

<?php require_once "common-footer.php"; ?>
