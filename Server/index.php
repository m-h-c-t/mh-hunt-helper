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
    <script defer src="/scripts/attraction.js"></script>
    <link rel="stylesheet" type="text/css" href="styles/attraction.css">
</head>
<body style="text-align: center;" class="text-center">
    <!-- Jumbotron -->
    <div class="jumbotron">
        <h1>Jack's MH Attraction Rate</h1>
        <a href="https://agiletravels.com" class="clickable"><span class="glyphicon glyphicon-chevron-left"></span> Jack's MH Tools</a>
    </div>
    <div class="container">
<?php

if (!empty($_GET['mouse'])) {
    print '<input id="prev_mouse" type="hidden" value="' . $_GET['mouse'] . '">';
}

print '

    <div class="input-group col-sm-6 col-sm-offset-3">
        <div class="input-group-addon"><strong>Mouse:</strong></div>
        <input name="mouse" id="mouse" class="form-control input-lg" type="text" placeholder="Start typing mouse name and select." autofocus>
        <div id="erase_mouse" class="input-group-addon fakebutton"><span class="glyphicon glyphicon-remove"></span></div>
    </div>
<br/>';

//Ajax here
print '<div id="results" class="table-responsive"></div>';

print '<br/><p class="text-center">For more info, copy of the data, or if you want to help with attraction rates, please look <a href="https://www.agiletravels.com">here</a>.</p>';
?>
</div>
<div id="loader" class="loader"></div>
<noscript id="deferred-styles">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="styles/attraction.css">
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