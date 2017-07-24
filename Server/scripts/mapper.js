$( function() {
    $("#erase_item").click(function () {
        $("#results").html('');
        $("#item").val('').focus();
        window.history.replaceState({}, "MH Hunt Helper", "mapper.php");
    });

    searchItems('all', firstLoad);

    function searchItems(item_id, callback) {
        if (item_id !== 'all') {
            $("#loader").css( "display", "block" );
            // Every time we search for a item (on reload or ajax) set a history of it.
            window.history.replaceState({}, "MH Hunt Helper", "mapper.php?item=" + item_id);
        }

        $.ajax({
            url: "searchByItem.php",
            method: "GET",
            data: {
                item_id: item_id,
                item_type: "map"
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
            source: function(request, response) {
                var results = $.ui.autocomplete.filter(items, request.term);
                response(results.slice(0, 10));
            },
            delay: 0,
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
        var final_html = '<table id="results_table" class="table table-striped table-hover"><thead><tr><th>Mouse</th><th>Seen Maps</th><th>Total Maps</th><th>Rate per map</th></tr></thead><tbody>';

        var all_stages = '';
        data.forEach(function(row) {
            final_html += '<tr><td>'
                + row.mouse + '</td><td>'
                + row.seen_maps + '</td><td>'
                + row.total_maps + '</td><td>'
                + ((row.rate)/100).toFixed(2) + '%</td></tr>';
        });
        final_html += '</tbody></table>';
        $("#results").html(final_html);
        $('#results_table').DataTable( {
            "paging":   false,
            "searching": false,
            "info": false,
            "order": [[3, 'desc']]
        });

        var table = $('#results_table').DataTable();
        table.columns().iterator( 'column', function (ctx, idx) {
            $( table.column(idx).header() ).append('<span class="sort-icon"/>');
        });

        $("#loader").css( "display", "none" );
    }
});
