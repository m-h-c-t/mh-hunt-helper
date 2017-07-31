<html lang="en">
<head>
    <meta charset="utf-8">
    <base href="/">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta property="og:title" content="Jack's MouseHunt Tools" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://www.agiletravels.com" />
    <meta property="og:description" content="Tools to help with the MouseHunt game." />
    <meta property="og:image" content="https://www.agiletravels.com/images/fb_image.jpg" />
    <meta property="fb:app_id" content="314857368939024" />

    <title>Jack's MH Tracker</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="styles/attraction.css">
</head>
<body style="text-align: center;" class="text-center">
    <!-- Jumbotron -->
    <div class="jumbotron">
        <h1>Jack's MH Tracker</h1>
        <a href="https://agiletravels.com" class="clickable"><span class="glyphicon glyphicon-chevron-left"></span> Jack's MH Tools</a>
    </div>
    <div class="container">
        <?php

        require_once "config.php";

        // PDO
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

        $query = $pdo->prepare("SELECT rh_environment, rh_timestamp FROM states");
        $query->execute();
        $states = $query->fetch(PDO::FETCH_ASSOC);

        if (!$states['rh_environment']) {
            $states['rh_environment'] = 'unknown';
            $states['rh_timestamp'] = 'a while';
        } else {
            $date = new DateTime($states['rh_timestamp']);
            $now = new DateTime();

            $states['rh_timestamp'] = $date->diff($now)->format("%h hour(s) and %i minute(s)");
        }

        ?>
        <h3>Current Relic Hunter Location: <?php print $states['rh_environment']; ?>.<br/>
            <small>Last seen: <?php print $states['rh_timestamp']; ?> ago.</small>
        </h3><br/><br/>

        <?php require_once "stats.php"; ?>

        <br/><p class="text-center">For more info, copy of the data, or if you want to help with data, please look <a href="https://www.agiletravels.com">here</a>.</p>
    </div>
    <?php include_once("ga.php") ?>
</body>
</html>
