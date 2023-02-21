<?php
    $title = "MHCT User History";
    // $css = "styles/loot.css";
    // $js = "scripts/attraction.js";
    $fluid_container = true;
    $load_datatable_libraries = true;
    $load_datatable_buttons = true;
    require_once "common-header.php";

if (empty($_GET['hunter_id'])) {
    print "<b>PLEASE SPECIFY A VALID USER ID</b>";
    ?><script>$("#loader").css( "display", "none" );</script><?php
    return;
}

$_REQUEST['hunter_id_hash'] = $_GET['hunter_id'];

define('not_direct_access', TRUE);
require_once "config.php";
require_once "db-connect.php";
require_once "check-userid.php";

$query2 = $pdo->prepare("SELECT count(*) FROM hunts where user_id = ?");
$query2->execute(array($user_id));
$count = $query2->fetchColumn();

if (empty($count)) {
    print "<br/>No hunts found<br/>";
    ?><script>$("#loader").css( "display", "none" );</script><?php
    return;
}
print 'Timezone is set to <span id="timezone_name">...</span>.Total hunts found: ' . $count . '.';
if ($count > 1000) {
    print " Limiting hunts to latest 1000.";
}
print "<br/>";

$query_string = '
    SELECT timestamp, l.name as location, GROUP_CONCAT(DISTINCT s.name SEPARATOR ", ") as stage, t.name as trap, b.name as base, ch.name as charm, h.shield, h.caught, m.name as mouse, c.name as cheese, GROUP_CONCAT(DISTINCT CONCAT_WS(" ", hl.amount, CONCAT(lt.name, IF(hl.lucky, "(L)", ""))) SEPARATOR ", ") as loot
    FROM (SELECT * from hunts WHERE user_id = ? ORDER BY id DESC LIMIT 1000) h
    INNER JOIN locations l on h.location_id = l.id
    LEFT JOIN hunt_stage hs on h.id = hs.hunt_id
    LEFT JOIN stages s on hs.stage_id = s.id
    LEFT JOIN mice m on h.mouse_id = m.id
    INNER JOIN cheese c on h.cheese_id = c.id
    INNER JOIN traps t on h.trap_id = t.id
    INNER JOIN bases b on h.base_id = b.id
    LEFT JOIN charms ch on h.charm_id = ch.id
    LEFT JOIN hunt_loot hl on h.id = hl.hunt_id
    LEFT JOIN loot lt on hl.loot_id = lt.id
    GROUP BY h.id
    ORDER BY timestamp DESC';
$query = $pdo->prepare($query_string);
$query->execute(array($user_id));
$results = $query->fetchAll(PDO::FETCH_ASSOC);

?>
    </div>
    <div class="table-responsive">
        <table id="results_table" class="table table-striped table-bordered table-hover display" style="width:100%"></table>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js" integrity="sha512-+H4iLjY3JsKiF2V6N366in5IQHj2uEsGV7Pp/GRcm0fn76aPAk5V8xB6n8fQhhSonTqTXs/klFz4D0GIn6Br9g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.40/moment-timezone-with-data-10-year-range.min.js" integrity="sha512-GKxhLkFh/5zSOuvIDwC5cdQkh13mR+jMgSA/9nBgA530xRXiwWhT7uje6b6Tpboa95M7OTSKxbYdMHRgLLBILQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script type="text/javascript">
        $('#timezone_name').html(Intl.DateTimeFormat().resolvedOptions().timeZone);
        var temp_date = new Date;
        $('#results_table').DataTable( {
            data: <?php echo json_encode($results) ?>,
            dom: 'lBftpri',
            order: [[0, 'desc']],
            buttons: [
            {
                extend: 'excelHtml5',
                title: 'MH Data export'
            },
            {
                extend: 'csvHtml5',
                title: 'MH Data export'
            },
            // Note: If wanted uncomment pdfMake and vfs_font libraries in commonFooter.php
            // {
            //     extend: 'pdfHtml5',
            //     title: 'MH Data export'
            // }
            ],
            columns: [
                { title: 'Time', data: 'timestamp' },
                { title: 'Location', data: 'location' },
                { title: 'Stage', data: 'stage' },
                { title: 'Trap', data: 'trap' },
                { title: 'Base', data: 'base' },
                { title: 'Charm', data: 'charm' },
                { title: 'Cheese', data: 'cheese', },
                { title: 'Shield', data: 'shield', className: 'text-center' },
                { title: 'Caught', data: 'caught', className: 'text-center' },
                { title: 'Mouse', data: 'mouse' },
                { title: 'Loot', data: 'loot', className: 'loot-col' },
            ],
            columnDefs: [
                {
                    // Loot col can get very hungry
                    targets: [-1],
                    width: '30%'
                },
                {
                    type: 'unix',
                    targets: 0,
                    render: (data, type, row) => {
                        return moment(data * 1000, 'x')
                            .tz(Intl.DateTimeFormat().resolvedOptions().timeZone)
                            .format('[<span style="white-space: nowrap;">]YYYY-MM-DD[</span> <span style="white-space: nowrap;">]HH:mm[</span>]')
                    }
                },
                {
                    "targets": [ 7, 8 ],
                    render: function ( data, type, row ) {
                        if (data === '1') return '<span class="text-success glyphicon glyphicon-ok"><span style="opacity:0">1</span></span>';
                        if (data === '0') return '<span class="text-danger glyphicon glyphicon-remove"><span style="opacity:0">0</span></span>';
                        return '';
                    }
                }
            ]
        });
        $("#loader").css( "display", "none" );
    </script>
<?php require_once "common-footer.php"; ?>
</body>
</html>
