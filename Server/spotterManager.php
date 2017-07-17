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
<div id="loader" class="loader"></div>
<div id="fb-root"></div>

<!-- Jumbotron -->
<div class="jumbotron">
    <div class="container-fluid">
        <h1>Jack's MH Map Spotter</h1>
        <p>Tool for map sniping, leeching and helping.</p>
        <a href="https://agiletravels.com" class="clickable"><span class="glyphicon glyphicon-chevron-left"></span> Jack's MH Tools</a>
    </div>
</div>

<div class="container">
    <a href="spotter.php" class="pull-left"><button class="btn btn-default">Back to all Requests / Offers</button></a><br/><br/>
</div>

<div id="newPostArea" class="container">
    <div class="col-md-6 col-md-offset-3">
    <!-- <fb:login-button id="fbLoginButton" class="hideOnLogin" scope="public_profile" onlogin="checkLoginState();">
    </fb:login-button>> -->
    <br class="hideOnLogin" />
    <div id="fbLoginButton" class="fb-login-button hideOnLogin" data-max-rows="1" data-size="large" data-button-type="login_with" data-show-faces="false" data-use-continue-as="false" onlogin="checkLoginState();" scope="public_profile"></div><br class="hideOnLogin" /><!--,publish_actions-->
    <p id="pleaseLogin" class="hideOnLogin">
        Please login to create a post. <br/>
        Facebook login is used to:</br>
        -Get your first name<br/>
        -Get link to your public fb profile<br/>
    </p>

    <form id="newPostForm" class="form-horizontal showOnLogin" method="POST">
        <h2>Create New Post</h2><br/>

        <div class="input-group">
            <span class="input-group-addon"><strong>Author</strong></span>
            <input type="text" class="form-control" name="fName" id="fName" readonly>
            <input type="hidden" id="fbUserId" name="fbUserId" />
            <input type="hidden" id="fbAccessToken" name="fbAccessToken" />
            <span id="logoutFB" style="cursor:pointer;" class="input-group-addon"><strong>FB Logout</strong></span>
        </div><br/>

        <div class="input-group">
            <span class="input-group-addon"><strong>Post Type</strong></span>
            <select id="postType" name="postType" class="form-control">
                <option value="snipe_request">Request a snipe</option>
                <option value="snipe_offer">Offer a snipe</option>
                <option value="leech_request">Request to leech</option>
                <option value="leech_offer">Offer a leech spot</option>
                <option value="helper_request">Request helper(s)</option>
                <option value="helper_offer">Offer to help</option>
            </select>
        </div><br/>

        <div id="mouseInputGroup">
            <div class="input-group">
                <span class="input-group-addon"><strong>Target Mouse</strong></span>
                <input id="mouseName" name="mouseName" type="text" class="form-control" required placeholder="Leprichaun!" maxlength="250" />
                <input type="hidden" id="mouseId" name="mouseId" required />
            </div><br/>
        </div>

        <div id="mapInputGroup">
            <div class="input-group">
                <span class="input-group-addon"><strong>Map Type</strong></span>
                <input id="mapName" name="mapName" type="text" class="form-control" required placeholder="Easy Treasure Map!" maxlength="250" />
                <input type="hidden" id="mapId" name="mapId" required/>
            </div>
            <div class="checkbox text-left">
            <label>
                <input id="mapDust" name="mapDust" type="checkbox" value="1" />
                <strong>Dusted</strong>
            </label>
            </div><br/>
        </div>

        <div id="rewardInputGroup">
            <div class="input-group">
                <span class="input-group-addon"><strong>Reward / Price</strong></span>
                <input id="rewardCount" name="rewardCount" type="number" class="form-control" min="1" max="1000" required />
                <span class="input-group-addon"><strong>SB+</strong></span>
            </div><br/>
        </div>

        <!--<div id="mapmeistersInputGroup">
            <div class="checkbox text-left">
                <label>
                    <input id="mapmeisters" name="mapmeisters" type="checkbox" value="1" disabled/>
                    <strong>Post it also to Mapmeisters Facebook Group (under construction)</strong>
                </label>
            </div>
            <textarea id="fbGroupMessage" name="fbGroupMessage" class="form-control" row="3" disabled></textarea><br/>
        </div>-->

        <span>Posts expire automatically after 48 hours.</span><br/>
        <span>This tool posts to this site and MH Discord.</span><br/><br/>
        <div class="row" style="clear:both;">
            <div class="alert alert-danger" id="error_message"></div>
            <div class="alert alert-success" id="success_message"></div>
        </div>
        <div class="row" style="clear:both;">
            <button class="btn btn-success" style="clear:all;" type="submit" id="newPostFormSubmit">Create Post</button><br/><br/>
        </div>
    </form>
    </div>
</div>

<!--<span>
    Please note: Your posts in Facebook Groups can be accessed by members of the group and are not restricted by the app privacy level.
</span><br/><br/>--><hr class="showOnLogin"/>

<div class="container showOnLogin">
    <h2>Your Posts</h2>
    <div id="currentRequests"></div>
</div><br/><br/>

<?php //include_once("ga.php") ?>
</body>
</html>
