$( function() {
    $("#erase_mouse").click(function () {
        $("#mouse").val('').focus();
    });

    searchMice('all', addAutocomplete);

    function searchMice(mouse_id, callback) {
        $.ajax({
            url: "searchByMouse.php",
            method: "POST",
            data: {
                mouse_id: mouse_id
            }
        })
        .done(function( data ) {
            callback( JSON.parse(data));
        });
    }

    function addAutocomplete(mice) {
        $('#mouse').autocomplete({
            source: mice,
            select: function( event, ui ) {
                $("#loader").css( "display", "block" );
                searchMice(ui.item.id, renderResultsTable);
            }
        });
    }

    function renderResultsTable(data) {
        var final_html = '<table id="results_table" class="table table-striped table-bordered"><thead><tr><th>Location</th><th>Stage</th><th>Cheese</th><th>Attracted</th><th>Total hunts</th><th>Rate</th></tr></thead><tbody>';

        var all_stages = '';
        data.forEach(function(row) {
            var stage = (row.stage ? row.stage : '');
            all_stages += stage;
            final_html += '<tr><td>' + row.location +
              '</td><td>' + stage +
              '</td><td>' + row.cheese +
              '</td><td>' + row.attracted_hunts +
              '</td><td>' + row.total_hunts +
              '</td><td>' + ((row.attracted_hunts/row.total_hunts)*100).toFixed(2) + '%</td></tr>';
        });
        final_html += '</tbody></table>';
        $("#results").html(final_html);
        $('#results_table').DataTable( {
            "paging":   false,
            "searching": false,
            "info": false,
            "order": [[5, 'desc']],
            "columnDefs": [
                {
                    "targets": [ 1 ],
                    "visible": (all_stages.length === 0 ? false : true)
                }
            ]
        });

        $("#loader").css( "display", "none" );
    }
});
