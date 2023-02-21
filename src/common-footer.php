<br/>
<?php if (!isset($hide_footer_note)) { ?>
    <p class="text-center">For more info, copy of the data, or if you want to help with data gathering, please look <a href="/">here</a>.
    <br/>Or click <a href="donations.php">here</a> to show your support through a donation.</p>
  </div></br>
<?php } ?>
<div id="loader" class="loader"></div>
<noscript id="deferred-styles">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" integrity="sha512-aOG0c6nPNzGk+5zjwyJaoRUgCdOrfSDhmMID2u4+OIslr0GjpLKo7Xm0Ao3xmpM4T8AmIouRkqwj1nrdVsLKEQ==" crossorigin="anonymous" />

    <?php if (isset($load_datatable_libraries)) { ?>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/dataTables.bootstrap.min.css" integrity="sha512-BMbq2It2D3J17/C7aRklzOODG1IQ3+MHw3ifzBHMBwGO/0yUqYmsStgBjI0z5EYlaDEFnvYV7gNYdD3vFLRKsA==" crossorigin="anonymous" />
        <link rel="stylesheet" type="text/css" href="styles/custom-datatables.css">
    <?php } ?>

    <?php if (isset($load_datatable_buttons)) { ?>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables.net-buttons-bs/2.3.4/buttons.bootstrap.min.css" integrity="sha512-9f6zdtjcsdQBi6t6rIC1qRCAvXJk+buIJqbDflhnw9EmpxcPSvJ7FPoDiA3R8UiaXaT8T5s+NNQePAMIB0MDpw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <?php } ?>

    <link rel="stylesheet" type="text/css" href="styles/common.css">
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
