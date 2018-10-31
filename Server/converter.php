<html lang="en">
<head>
    <title>Jack's MH Converter</title>
    <?php require "common_head.php"; ?>
    <script defer src="/scripts/converter.js"></script>
</head>
<body style="text-align: center;" class="text-center">
    <!-- Jumbotron -->
    <div class="jumbotron">
        <h1>Jack's MH Converter</h1>
        <p>Search contents by convertible name.</p>
        <a href="https://agiletravels.com" class="clickable"><span class="glyphicon glyphicon-chevron-left"></span> Jack's MH Tools</a>
    </div>
    <div class="container">
        <input id="item_type" type="hidden" value="convertible">
        <?php

        if (!empty($_GET['item'])) {
            print '<input id="prev_item" type="hidden" value="' . $_GET['item'] . '">';
        }

        print '
            <div class="input-group col-sm-6 col-sm-offset-3">
                <div class="input-group-addon"><strong>Convertible:</strong></div>
                <input name="item" id="item" class="form-control input-lg" type="text" placeholder="Start typing convertible name and select." autofocus>
                <div id="erase_item" class="input-group-addon fakebutton"><span class="glyphicon glyphicon-remove"></span></div>
            </div>
        <br/>';

        //Ajax here
        print '<div id="results_total"></div>';
        print '<div id="results" class="table-responsive"></div>';

        print '<br/><p class="text-center">For more info, copy of the data, or if you want to help with data gathering, please look <a href="https://www.agiletravels.com">here</a>.</p>';
        ?>
    </div>
    <?php require "common_footer.php"; ?>
    <?php include_once("ga.php") ?>
</body>
</html>
