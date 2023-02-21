<html lang="en">
<head>
    <title><?php echo $title; ?></title>
    <meta name="wot-verification" content="91f659cc74820ead4528"/>
    <meta charset="utf-8">
    <base href="/">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta property="og:title" content="MHCT MouseHunt Tools" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://www.mhct.win" />
    <meta property="og:description" content="Tools to help with the MouseHunt game." />
    <meta property="og:image" content="https://www.mhct.win/images/fb_image.jpg" />

    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha512-Dop/vW3iOtayerlYAqCgkVr2aTr2ErwwTYOvRFUpzl2VhCMJyjQF0Q9TjUXIo6JhuM/3i0vVEt2e/7QQmnHQqw==" crossorigin="anonymous" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>

    <?php if (isset($load_datatable_libraries)) { ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js" integrity="sha512-BkpSL20WETFylMrcirBahHfSnY++H2O1W+UnEEO4yNIl+jI2+zowyoGJpbtk6bx97fBXf++WJHSSK2MV4ghPcg==" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/dataTables.bootstrap.min.js" integrity="sha512-F0E+jKGaUC90odiinxkfeS3zm9uUT1/lpusNtgXboaMdA3QFMUez0pBmAeXGXtGxoGZg3bLmrkSkbK1quua4/Q==" crossorigin="anonymous"></script>
    <?php } ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" integrity="sha512-uto9mlQzrs59VwILcLiRYeLKPPbS/bT71da/OEBYEwcdNUk8jYIy+D176RYoop1Da+f9mvkYrmj5MCLZWEtQuA==" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.1/js/bootstrap.min.js" integrity="sha512-oBTprMeNEKCnqfuqKd6sbvFzmFQtlXS3e0C/RGFV0hD6QzhHV+ODfaQbAlmY6/q0ubbwlAM/nCJjkrgA3waLzg==" crossorigin="anonymous"></script>

    <!-- Optimize loading csv+excel+pdf export libraries on pages that need it -->
    <?php if (isset($load_datatable_buttons)) { ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables.net-buttons/2.3.4/js/dataTables.buttons.min.js" integrity="sha512-c0EQQ0rxKGBgWqV3cSbC9sVQqqwC54dYBQZ7u+fBX2RiZErCKkR2Gh/+UzQrLPFGXJBZUF7z/0WLFfsJet0O1w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables.net-buttons-bs/2.3.4/buttons.bootstrap.min.js" integrity="sha512-fKh5VYAYB2x9z21kUiRJLypt6ePYzjaa/c8JMhj6B3MEIEi1lGEAgbWUJoVjyAZbJTJ7FDk+starRSMq4QDklg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables.net-buttons/2.3.4/js/buttons.html5.min.js" integrity="sha512-cBlHTLVISzl4A2An/1uQCqUq7MPJlCTqk/Uvwf1OU8lAB87V72oPdllhBD7hYpSDhmcOqY/PIeJ5bUN/EHcgpw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js" integrity="sha512-XMVd28F1oH/O71fzwBnV7HucLxVwtxf26XV8P4wPk26EDxuGZ91N8bsOttmnomcCD3CS5ZMRL50H0GgOHvegtg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.72/pdfmake.min.js" integrity="sha512-/POIba1QMKIR+IWgQ3m3XU3zD9HXz0b5cc1Wmg8zfijO+Db+LZH0NXrUZqKJ/HLtODY3mziQF6Ppc8lcBOySLA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
        <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.72/vfs_fonts.min.js" integrity="sha512-BDZ+kFMtxV2ljEa7OWUu0wuay/PAsJ2yeRsBegaSgdUhqIno33xmD9v3m+a2M3Bdn5xbtJtsJ9sSULmNBjCgYw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
    <?php } ?>

    <?php if (isset($js)) { echo "<script defer src=\"$js\"></script>"; } ?>
    <?php if (isset($css)) { echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$css\">"; } ?>
</head>
<body style="text-align: center;" class="text-center">
    <!-- Jumbotron -->
    <div class="jumbotron">
        <h1><?php echo $title; ?></h1>
        <?php if (!isset($hide_home_link)) { ?> <a href="/" class="clickable"><span class="glyphicon glyphicon-chevron-left"></span> Main Page</a><?php } ?>
    </div>
    <div class="<?php echo isset($fluid_container) ? 'container-fluid' : 'container'; ?>">
