<?php
    $title = "MHCT Donations";
    require_once "common-header.php";
?>
<h3>Thanks again to our sponsors for keeping our servers alive!</h3>
<h4>If you would like to donate to support the cost of our servers, you may do so here:</h4>
<div class="container">
<div class="panel-group col-md-6 col-md-offset-3" id="donate_accordion" role="tablist" aria-multiselectable="true">
    <!-- Ko-Fi -->
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingOne">
        <h4 class="panel-title">
            <a style="display:block;text-decoration:none;" role="button" data-toggle="collapse" data-parent="#donate_accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                Ko-Fi (no registration required)
            </a>
        </h4>
        </div>
        <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
        <div class="panel-body">
            <a href="https://ko-fi.com/mousehuntcommunitytools" style="display:block;text-decoration:none;color:#333;">Click here to donate</a>
        </div>
        </div>
    </div>

    <!-- PayPal -->
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingTwo">
        <h4 class="panel-title">
            <a style="display:block;text-decoration:none;" role="button" data-toggle="collapse" data-parent="#donate_accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                PayPal
            </a>
        </h4>
        </div>
        <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
        <div class="panel-body">
            <a href="https://www.paypal.com/paypalme/mhcommtools" style="display:block;text-decoration:none;color:#333;">Click here to donate</a>
        </div>
        </div>
    </div>

</div></div>

<?php
    require_once "common-footer.php";
?>
