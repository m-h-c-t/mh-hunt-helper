$( function() {
    $("#erase_item").click(function () {
        $("#results").html('');
        $("#item").val('').focus();
        window.history.replaceState({}, "MH Hunt Helper", "loot.php");
    });

    searchItems('all', firstLoad);

    function searchItems(item_id, callback) {
        if (item_id !== 'all') {
            $("#loader").css( "display", "block" );
            // Every time we search for a item (on reload or ajax) set a history of it.
            window.history.replaceState({}, "MH Hunt Helper", "loot.php?item=" + item_id);
        }

        $.ajax({
            url: "searchByItem.php",
            method: "POST",
            data: {
                item_id: item_id,
                item_type: "loot"
            }
        })
        .done(function( data ) {
            callback( JSON.parse(data));
        });
    }

    function firstLoad(items) {
        // Check and search for previous item (done on reload of whole page)
        var previous_item_id = $("#prev_item").val();
        if (previous_item_id) {
            var previous_item_name = '';
            for (var i = 0; i < items.length; i++) {
                if (items[i].id == previous_item_id) {
                    previous_item_name = items[i].value;
                    $("#item").val(previous_item_name);
                    break;
                }
            }
            searchItems($("#prev_item").val(), renderResultsTable);
        }

        // set autocomplete
        addAutocomplete(items);
    }

    function addAutocomplete(items) {
        $('#item').autocomplete({
            source: items,
            select: function( event, ui ) {
                searchItems(ui.item.id, renderResultsTable);
            }
        });

        // Fix for double click on IOS
        if (navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
            $('#item').autocomplete('widget').off('mouseenter');
        }
    }

    function renderResultsTable(data) {
        var final_html = '<table id="results_table" class="table table-striped table-hover"><thead><tr><th>Location</th><th>Stage</th><th>Cheese</th><th>Rate per hunt</th><th>Hunts</th></tr></thead><tbody>';

        var all_stages = '';
        data.forEach(function(row) {
            var stage = (row.stage ? row.stage : '');
            all_stages += stage;
            final_html += '<tr><td>'
                + row.location + '</td><td>'
                + stage + '</td><td>'
                + row.cheese + '</td><td>'
                + ((row.rate)/100).toFixed(2) + '</td><td>'
                + row.total_hunts + '</td></tr>';
        });
        final_html += '</tbody></table>';
        $("#results").html(final_html);
        $('#results_table').DataTable( {
            "paging":   false,
            "searching": false,
            "info": false,
            "order": [[3, 'desc']],
            "columnDefs": [
                {
                    "targets": [ 1 ],
                    "visible": (all_stages.length === 0 ? false : true)
                }
            ]
        });

        var table = $('#results_table').DataTable();
        table.columns().iterator( 'column', function (ctx, idx) {
            $( table.column(idx).header() ).append('<span class="sort-icon"/>');
        });

        $("#loader").css( "display", "none" );
    }
});
