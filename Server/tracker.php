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
        $file_name = 'tracker.json';
        $location = 'Unknown';
        $time_since = 'a while';
        $data = file_get_contents($file_name);

        if (!empty($data)) {
            $data = json_decode($data);

            if (!empty($data->rh->location)) {
                $location = $data->rh->location;
            }

            if ($data->rh->last_seen != 0) {
                $last_seen = $data->rh->last_seen;
                $date = new DateTime("@$last_seen");
                $now = new DateTime();

                $time_since = $date->diff($now)->format("%h hour(s) and %i minute(s)");
            }
        }

        ?>
        <h3>Current Relic Hunter Location: <?php print $location; ?>.<br/>
            <small>Last seen: <?php print $time_since; ?> ago.</small>
        </h3><br/><br/>

        <?php require_once "stats.php"; ?><br/><br/>
        <?php require_once "missing_mice.php"; ?>

        <br/><p class="text-center">For more info, copy of the data, or if you want to help with data gathering, please look <a href="https://www.agiletravels.com">here</a>.</p>
    </div>
    <?php include_once("ga.php") ?>
</body>
</html>
