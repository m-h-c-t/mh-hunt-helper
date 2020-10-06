<html lang="en">
<head>
    <?php require "common_head.php"; ?>
    <script defer src="/scripts/attraction.js"></script>
</head>
<body style="text-align: center;" class="text-center">
    <!-- Jumbotron -->
    <div class="jumbotron">
        <h1>MHCT Attraction Rate</h1>
        <a href="/" class="clickable"><span class="glyphicon glyphicon-chevron-left"></span> MHCT Tools</a>
    </div>
    <div class="container">

        <input id="prev_mouse" type="hidden" value="<?php (!empty($_GET['mouse']) ? print $_GET['mouse'] : '' ) ?>">
        <input id="prev_timefilter" type="hidden" value="<?php (!empty($_GET['timefilter']) ? print $_GET['timefilter'] : 'all' ) ?>">

    <div class="input-group col-sm-6 col-sm-offset-3">
        <div class="input-group-addon"><strong style="display:block">Mouse:</strong><br/><br/><strong style="display:block">Time:</strong></div>
        <input name="mouse" id="mouse" class="form-control input-lg" type="text" placeholder="Start typing mouse name and select." autofocus>
        <select class="form-control input-lg" id="timefilter" name="timefilter">
        <?php
            $silent = true;
            require_once "filters.php";
            foreach ($filters as $filter) {
                print '<option value="' . $filter['code_name'] . '">' . $filter['display_name'] . '</option>';
            }
        ?>
        </select>
        <div id="erase_mouse" class="input-group-addon fakebutton"><span class="glyphicon glyphicon-remove"></span></div>
    </div>
    <br/>

    <div id="results" class="table-responsive"></div>

    <br/><p class="text-center">For more info, copy of the data, or if you want to help with data gathering, please look <a href="/">here</a>.</p>

</div>
<div id="loader" class="loader"></div>
<?php require "common_footer.php"; ?>
</body>
</html>
