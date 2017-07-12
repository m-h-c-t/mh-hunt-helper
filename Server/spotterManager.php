<!DOCTYPE html>
<html>
<head>
    <title>Jack's MH Map Spotter</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <link rel="stylesheet" type="text/css" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.min.css">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/attraction.css">
    <style>
        .showOnLogin, .hideOnLogin, #error_message, #success_message {
            display: none;
        }
    </style>
    <script defer src="scripts/spotter.js"></script>
</head>

<body class="text-center" style="background-color: #fff;">
<div id="fb-root"></div>

<!-- Jumbotron -->
<div class="jumbotron">
    <div class="container-fluid">
        <h1>Jack's MH Map Spotter</h1>
        <p>Tool for map sniping.</p>
        <a href="https://agiletravels.com" class="clickable"><span class="glyphicon glyphicon-chevron-left"></span> Jack's MH Tools</a>
    </div>
</div>

<div class="container">
    <a href="spotter.php" class="pull-left"><button class="btn btn-default">Back to all Snipe Requests</button></a><br/><br/>
</div><br/>

<div id="newPostArea" class="container">
    <div class="col-md-6 col-md-offset-3">
    <!-- <fb:login-button id="fbLoginButton" class="hideOnLogin" scope="public_profile" onlogin="checkLoginState();">
    </fb:login-button>> -->
    <div id="fbLoginButton" class="fb-login-button hideOnLogin" data-max-rows="1" data-size="large" data-button-type="login_with" data-show-faces="false" data-use-continue-as="false" onlogin="checkLoginState();"></div><br/>
    <span id="pleaseLogin" class="hideOnLogin">Please login to create snipe request.</span>
    <form id="newPostForm" class="form-horizontal showOnLogin" method="POST">
        <h2>New Snipe Request</h2><br/>

        <div class="input-group">
            <span class="input-group-addon"><strong>Author</strong></span>
            <input type="text" class="form-control" name="fName" id="fName" readonly>
            <input type="hidden" id="fbLink" name="fbLink" />
            <input type="hidden" id="fbUserId" name="fbUserId" />
            <input type="hidden" id="fbAccessToken" name="fbAccessToken" />
            <span id="logoutFB" style="cursor:pointer;" class="input-group-addon"><strong>FB Logout</strong></span>
        </div><br/>

        <div class="input-group" id="mouseInputGroup">
            <span class="input-group-addon"><strong>Target Mouse</strong></span>
            <input id="mouseName" name="mouseName" type="text" class="form-control" required placeholder="Leprichaun!" maxlength="250" />
            <input type="hidden" id="mouseId" name="mouseId" />
        </div><br/>

        <div class="input-group" id="rewardInputGroup">
            <span class="input-group-addon"><strong>Reward</strong></span>
            <input id="rewardCount" name="rewardCount" type="number" class="form-control" required />
            <span class="input-group-addon"><strong>SB+</strong></span>
        </div><br/>

        Automatically expires in 72 hours after posting.<br/><br/>
        <div class="row" style="clear:both;">
            <button class="btn btn-success" style="clear:all;" type="submit" id="newPostFormSubmit">Post Request</button><br/><br/>
        </div>
    </form>
    </div>
    <div class="row" style="clear:both;">
        <span class="alert alert-danger" id="error_message"></span>
        <span class="alert alert-success" id="success_message"></span>
    </div>
</div>

<div class="container showOnLogin">
    <h2>All your requests</h2>
    <div id="currentRequests"></div>
</div><br/><br/>

<script>
    //console.log(<?php echo json_encode($_POST); ?>);
</script>
<?php //include_once("ga.php") ?>
</body>
</html>
