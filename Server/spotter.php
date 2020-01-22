This tool has been retired :(
<?php return; // OFF SWITCH ?>
<!DOCTYPE html>
<html>
<head>
    <title>MHCT Map Spotter</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta property="og:title" content="MHCT MouseHunt Tools" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://www.agiletravels.com" />
    <meta property="og:description" content="Tools to help with the MouseHunt game." />
    <meta property="og:image" content="https://www.agiletravels.com/images/fb_image.jpg" />
    <meta property="fb:app_id" content="314857368939024" />

    <script defer src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <script defer src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script defer src="scripts/spotter.js"></script>
    <link rel="stylesheet" type="text/css" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.min.css">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/attraction.css">
</head>

<body class="text-center" style="background-color: #fff;">
<div id="loader" class="loader"></div>

<?php if (empty($_GET['nokitty'])) { ?>
<!-- Jumbotron -->
<div class="jumbotron">
    <div class="container-fluid">
        <h1>MHCT Map Spotter</h1>
        <p>Tool for map sniping, leeching and helping.</p>
        <a href="https://agiletravels.com" class="clickable"><span class="glyphicon glyphicon-chevron-left"></span> MHCT Tools</a>
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

<?php //include_once("ga.php") ?>
</body>
</html>
