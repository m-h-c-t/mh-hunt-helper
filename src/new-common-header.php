<html lang="en">
<head>
    <title><?php echo $title; ?></title>
    <?php require "common_head.php"; ?>
    <?php if ($js) { echo "<script defer src=\"$js\"></script>"; } ?>
    <?php if ($css) { echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$css\">"; } ?>
</head>
<body style="text-align: center;" class="text-center">
    <!-- Jumbotron -->
    <div class="jumbotron">
        <h1><?php echo $title; ?></h1>
        <a href="/" class="clickable"><span class="glyphicon glyphicon-chevron-left"></span> MHCT Tools</a>
    </div>
    <div class="container-fluid">
