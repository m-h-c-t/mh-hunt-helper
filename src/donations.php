<?php
    $title = "MHCT Donations";
    require_once "common-header.php";
?>
<h3>Thanks again to our sponsors for keeping our servers alive!</h3>
<h4>If you would like to donate to support the cost of our servers, you may do so here:</h4>
<div class="container">
<div class="panel-group col-md-6 col-md-offset-3" id="donate_accordion" role="tablist" aria-multiselectable="true">
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingOne">
        <h4 class="panel-title">
            <a style="display:block;text-decoration:none;" role="button" data-toggle="collapse" data-parent="#donate_accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                Patreon
            </a>
        </h4>
        </div>
        <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
        <div class="panel-body">
            <a href="https://www.patreon.com/mhct" style="display:block;text-decoration:none;color:#333;">Donate using Patreon here.</a>
        </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingTwo">
        <h4 class="panel-title">
            <a style="display:block;text-decoration:none;" class="collapsed" role="button" data-toggle="collapse" data-parent="#donate_accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                Bitcoin
            </a>
        </h4>
        </div>
        <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
        <div class="panel-body">
            <img style="height:300;width:300;" src="images/bitcoin_qr.jpg"><br/>
            <button type="button" class="btn btn-default" onclick="navigator.clipboard.writeText('bc1quq8wtjwylkh6xh0q3wdgp74t8zyjyvnpakf4h2');">
                Copy address <span class="glyphicon glyphicon-copy" aria-hidden="true"></span>
            </button>
        </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingThree">
        <h4 class="panel-title">
            <a style="display:block;text-decoration:none;" class="collapsed" role="button" data-toggle="collapse" data-parent="#donate_accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                Ethereum
            </a>
        </h4>
        </div>
        <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
        <div class="panel-body">
            <img style="height:300;width:300;" src="images/eth_qr.jpeg"><br/>
            <button type="button" class="btn btn-default" onclick="navigator.clipboard.writeText('0x0519F3dB4C7b2C87EAe6F06759DdC4697A5fD96d');">
                Copy address <span class="glyphicon glyphicon-copy" aria-hidden="true"></span>
            </button>
        </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingFour">
        <h4 class="panel-title">
            <a style="display:block;text-decoration:none;" class="collapsed" role="button" data-toggle="collapse" data-parent="#donate_accordion" href="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                Monero
            </a>
        </h4>
        </div>
        <div id="collapseFour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFour">
        <div class="panel-body">
            <img style="height:300;width:300;" src="images/monero_qr.png"><br/>
            <button type="button" class="btn btn-default" onclick="navigator.clipboard.writeText('49tfggRG3XjezzLK2tB983BPTggqJx4JXCujTie23hzh29DfeAHPYvD7y5f2hAZdoXhr4gJJkUPuCDeU2iwiS1QQFevmPMt');">
                Copy address <span class="glyphicon glyphicon-copy" aria-hidden="true"></span>
            </button>
        </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingFive">
        <h4 class="panel-title">
            <a style="display:block;text-decoration:none;" class="collapsed" role="button" data-toggle="collapse" data-parent="#donate_accordion" href="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                Algorand
            </a>
        </h4>
        </div>
        <div id="collapseFive" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFive">
        <div class="panel-body">
            <img style="height:300;width:300;" src="images/algo_qr.jpeg"><br/>
            <button type="button" class="btn btn-default" onclick="navigator.clipboard.writeText('TA7P52M3W6KPJLUXMNADFQWSFSHRODUFTWJBYSAQFEHYON7S26TBF4EJWU');">
                Copy address <span class="glyphicon glyphicon-copy" aria-hidden="true"></span>
            </button>
        </div>
        </div>
    </div>
</div></div>

<?php
    require_once "common-footer.php";
?>
