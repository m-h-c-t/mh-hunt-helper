<?php
    $title = "MHCT Contributors";
    require_once "common-header.php";
?>
<div class="container">
    <h3>Special thanks to all the contributors and sponsors. We could not have made it without you.<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="red" class="bi bi-balloon-heart-fill" viewBox="0 0 16 16">
    <path fill-rule="evenodd" d="M8.49 10.92C19.412 3.382 11.28-2.387 8 .986 4.719-2.387-3.413 3.382 7.51 10.92l-.234.468a.25.25 0 1 0 .448.224l.04-.08c.009.17.024.315.051.45.068.344.208.622.448 1.102l.013.028c.212.422.182.85.05 1.246-.135.402-.366.751-.534 1.003a.25.25 0 0 0 .416.278l.004-.007c.166-.248.431-.646.588-1.115.16-.479.212-1.051-.076-1.629-.258-.515-.365-.732-.419-1.004a2.376 2.376 0 0 1-.037-.289l.008.017a.25.25 0 1 0 .448-.224l-.235-.468ZM6.726 1.269c-1.167-.61-2.8-.142-3.454 1.135-.237.463-.36 1.08-.202 1.85.055.27.467.197.527-.071.285-1.256 1.177-2.462 2.989-2.528.234-.008.348-.278.14-.386Z"/>
    </svg></h3>
    <p class="muted">(In no particular order. Please message me on Discord if you would like to be added/updated/removed. My memory is not great, and if I missed you, it was not on purpose.)</p>
</div>
<div class="container-fluid">
    <div class="col-md-12" style="height:500px;" id="my_canvas"></div>
    <script type="text/javascript" src="scripts/wordcloud2.js"></script>
    <script type="text/javascript">WordCloud(document.getElementById('my_canvas'), {
        shrinkToFit: true,
        minSize: 12,
        // origin: [500, 0],
        clearCanvas: true,
        gridSize: 15,
        weightFactor: 4,
        color: 'random-dark',
        // hover: window.drawBox,
        fontFamily: 'Finger Paint, cursive, sans-serif',
        rotateRatio: 0,
        // rotationSteps: 2,
        // drawOutOfBound: true,
        // shape: 'cardioid',
        list: [
            ['Aardwolf', 10],
            ['Asterios', 10],
            ['BT', 10],
            ['Bavovanachte', 10],
            ['bradp', 10],
            ['CBS', 10],
            ['CC Cat', 10],
            ['Chad', 10],
            ['Chromatical', 10],
            ['Coding-Hen', 10],
            ['Cube', 10],
            ['Groupsky', 10],
            ['Haoala', 10],
            ['Hazado', 10],
            ['in59te', 10],
            ['Jack', 10],
            ['Jeanie', 10],
            ['Jemsterr', 10],
            ['Kuh', 10],
            ['Larry the Friendly Knight', 10],
            ['Leppy', 10],
            ['Limerencee', 10],
            ['Michele', 10],
            ['Ms. Crizzly', 10],
            ['Mistborn94', 10],
            ['Mooreb0314', 10],
            ['Neb', 10],
            ['Nick (Horntracker)', 10],
            ['Oxyride', 10],
            ['PersonalPalimpsest', 10],
            ['Plasmoidia', 10],
            ['PotatoSalad', 10],
            ['Program', 10],
            ['Renette', 10],
            ['Ryonn', 10],
            ['Selianth', 10],
            ['Silvermane', 10],
            ['Sophie', 10],
            ['Soya', 10],
            ['Squirrely', 10],
            ['StijnJacobsPXL', 10],
            ['Tehhowch', 10],
            ['Tsitu', 10],
            ['w0en', 10],
            ['Warden Slayer', 10],
            ['Wynaut', 10],
            ['Xalca', 10],
            ['Wei', 10],
            ['Xellis', 10],
            ['YellowGreen', 10],
        ]
    } );</script>
</div>
<?php
    require_once "common-footer.php";
?>
