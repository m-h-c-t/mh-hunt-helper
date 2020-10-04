This tool has been retired :(
<?php return; // OFF SWITCH ?>
<!DOCTYPE html>
<html>
<head>
    <title>MHCT Map Spotter</title>
    <?php require "common_head.php"; ?>
    <script defer src="scripts/spotter.js"></script>
</head>

<body class="text-center" style="background-color: #fff;">
<div id="loader" class="loader"></div>

<?php if (empty($_GET['nokitty'])) { ?>
<!-- Jumbotron -->
<div class="jumbotron">
    <div class="container-fluid">
        <h1>MHCT Map Spotter</h1>
        <p>Tool for map sniping, leeching and helping.</p>
        <a href="/" class="clickable"><span class="glyphicon glyphicon-chevron-left"></span> MHCT Tools</a>
    </div>
</div>
<?php } else { ?>
<br/>
<?php } ?>
<div class="container">
    <a href="spotterManager.php<?php print (empty($_GET['nokitty']) ? '' : '?nokitty=1'); ?>" class="pull-right"><button class="btn btn-success">Create/Manage Posts</button></a><br/><br/>

    <span>All requests automatically removed 48 hours after posting. Sorted by oldest first.</span><br/>
    <span>Stay vigilant! Check if the other party is in a FB Maps Group Like Mapmeisters, LMM, MMS, etc.</span><br/>

    <div class="btn-group" role="group" id="typeFilters">
        <button type="button" class="btn btn-default active" value="">All posts</button>
        <button type="button" class="btn btn-default" value="snipe_request">Snipe Requests</button>
        <button type="button" class="btn btn-default" value="snipe_offer">Snipe Offers</button>
        <button type="button" class="btn btn-default" value="leech_request">Leech Requests</button>
        <button type="button" class="btn btn-default" value="leech_offer">Leech Offers</button>
        <button type="button" class="btn btn-default" value="helper_request">Helper Requests</button>
        <button type="button" class="btn btn-default" value="helper_offer">Helper Offers</button>
    </div><br/><br/>
    <div id="currentRequests"></div>
</div><br/><br/>
<?php require "common_footer.php"; ?>
</body>
</html>
