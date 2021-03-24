<?php
    $title = "MHCT Mapper";
    // $css = "styles/loot.css";
    $js = "scripts/mapper.js";
    $load_datatable_libraries = true;
    require_once "common-header.php";
?>

        <input id="item_type" type="hidden" value="loot">
        <?php
        if (!empty($_GET['item'])) {
            print '<input id="prev_item" type="hidden" value="' . $_GET['item'] . '">';
        } ?>

        <div class="input-group col-sm-6 col-sm-offset-3">
            <div class="input-group-addon"><strong>Map:</strong></div>
            <input name="item" id="item" class="form-control input-lg" type="text" placeholder="Start typing map name and select." autofocus>
            <div id="erase_item" class="input-group-addon fakebutton"><span class="glyphicon glyphicon-remove"></span></div>
        </div>
        <br/>

        <div id="results_total"></div>
        <div id="results" class="table-responsive"></div>
    </div>
<?php require_once "common-footer.php"; ?>
