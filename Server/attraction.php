<html lang="en">
<head>
<meta charset="utf-8">
    <base href="/">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">

    <script defer src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <script defer src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
    <script defer src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
    <script defer src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script defer src="attraction.js"></script>
</head>
<body style="text-align: center;" class="text-center">
    <div class="container">
        <h1 class="text-center">Attraction Rate</h1><br/><br/>
<?php

require "config.php";

// PDO
$pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

$query = $pdo->prepare('SELECT COUNT(*) as hunts, COUNT(DISTINCT user_id) as users, COUNT(DISTINCT mouse_id) as mice, COUNT(DISTINCT location_id) as locations, COUNT(DISTINCT stage_id) as stages, COUNT(DISTINCT trap_id) as traps, COUNT(DISTINCT cheese_id) as cheese, COUNT(DISTINCT base_id) as bases, COUNT(DISTINCT charm_id) as charms FROM hunts');
if (!$query->execute()) {
    echo 'Select all hunts and users failed';
    return;
}
$row = $query->fetch(PDO::FETCH_ASSOC);


print '

    <div class="input-group col-sm-4 col-sm-offset-4">
        <div class="input-group-addon"><strong>Mouse:</strong></div>
        <input name="mouse" id="mouse" class="form-control input-lg" type="text" placeholder="Type mouse name and select." autofocus>
        <div id="erase_mouse" class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></div>
    </div>
<br/>';

//Ajax here
print '<div id="results"></div>';

print '<br/><p class="text-center">This is in very early stages of development. Much more to come. <a href="updates.txt">Check out latest updates here</a>.<br/> If you want to help, please install <a href="https://chrome.google.com/webstore/detail/mh-hunt-helper/ghfmjkamilolkalibpmokjigalmncfek">this Chrome extension</a>.<br/>Install it on Opera using <a href="https://addons.opera.com/en/extensions/details/download-chrome-extension-9/">this</a> and on Firefox using <a href="https://addons.mozilla.org/en-US/firefox/addon/chrome-store-foxified/">this</a>.<br/><a href="https://github.com/DevJackSmith/mh-hunt-helper">Open source is available here.</a>
    <br/><h4>Stats so far</h4>Contributors: ' . $row['users'] . '<br/>Hunts: ' . $row['hunts'] . '<br/>Traps: ' . $row['traps'] . '<br/>Bases: ' . $row['bases'] . '<br/>Charms: ' . $row['charms'] . '<br/>Cheese: ' . $row['cheese'] . '<br/>Mice: ' . $row['mice'] .'<br/>Locations: ' . $row['locations'] .'<br/>Stages: ' . $row['stages'] . '<br/>Current version: 1.5</p>';
?>
</div>
<div id="loader" class="loader"></div>
    <noscript id="deferred-styles">
        <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.min.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="attraction.css">
    </noscript>
    <script>
      var loadDeferredStyles = function() {
        var addStylesNode = document.getElementById("deferred-styles");
        var replacement = document.createElement("div");
        replacement.innerHTML = addStylesNode.textContent;
        document.body.appendChild(replacement)
        addStylesNode.parentElement.removeChild(addStylesNode);
      };
      var raf = requestAnimationFrame || mozRequestAnimationFrame ||
          webkitRequestAnimationFrame || msRequestAnimationFrame;
      if (raf) raf(function() { window.setTimeout(loadDeferredStyles, 0); });
      else window.addEventListener('load', loadDeferredStyles);
    </script>
</body>
</html>