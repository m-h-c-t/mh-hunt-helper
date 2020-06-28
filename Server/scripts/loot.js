$(function() {
    $("#erase_item").click(function() {
        $("#results").html('');
        $("#item").val('').focus();
        $('#timefilter').val('all_time');
        $("#prev_item").val('');
        $("#prev_timefilter").val('');
        $("#rate_per_hunt").val('');
        window.history.replaceState({}, "MH Hunt Helper", "loot.php");
    });

    searchItems('all', firstLoad, $('#timefilter').val());

    function searchItems(item_id, callback, timefilter) {
        if (!item_id) return;

        if (item_id !== 'all') {
            $("#loader").css("display", "block");
            // Every time we search for a item (on reload or ajax) set a history of it.
            window.history.replaceState({}, "MH Hunt Helper", "loot.php?item=" + item_id + "&timefilter=" + timefilter);
            $("#prev_item").val(item_id);
            $("#prev_timefilter").val(timefilter);
        }

        $.ajax({
            url: "searchByItem.php",
            method: "POST",
            data: {
                item_id: item_id,
                item_type: "loot",
                timefilter: timefilter
            }
        }).done(function(data) {
            callback(JSON.parse(data));
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
            if ($('#prev_timefilter').val()) {
                $('#timefilter').val($('#prev_timefilter').val());
            }
            searchItems($("#prev_item").val(), renderResultsTable, $('#timefilter').val());
        }

        // set autocomplete
        addAutocomplete(items);
        $('#timefilter').change(function() {
            searchItems($('#prev_item').val(), renderResultsTable, $('#timefilter').val());
        });
        $('#rate_per_hunt').change(function() {
            searchItems($('#prev_item').val(), renderResultsTable, $('#timefilter').val());
        });
    }

    function addAutocomplete(items) {
        $('#item').autocomplete({
            source: function(request, response) {
                var results = $.ui.autocomplete.filter(items, request.term);
                response(results.slice(0, 10));
            },
            delay: 0,
            select: function(event, ui) {
                searchItems(ui.item.id, renderResultsTable, $('#timefilter').val());
            }
        });

        // Fix for double click on IOS
        if (navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
            $('#item').autocomplete('widget').off('mouseenter');
        }
    }

    function renderResultsTable(data) {
        var show_per_hunt = $('#rate_per_hunt').is(':checked');
        var per_hunt_catch = '<th>Qty / Catch</th><th>Catches</th>'; // Per catch by default
        if (show_per_hunt) {
            per_hunt_catch = '<th>Qty / Hunt</th><th>Hunts</th>';
        }
        var final_html = '<table id="results_table" class="table table-striped table-hover" style="width:100%"><thead><tr><th>Location</th><th>Stage</th><th>Cheese</th>' + per_hunt_catch + '<th>Drop Chance</th><th title="95% CI Margin of Error for Drop Chance">± Error</th><th>Qty Range</th></tr></thead><tbody>';

        var all_stages = '';
        data.forEach(function(row) {
            var stage = (row.stage ? row.stage : '');
            all_stages += stage;
            final_html += '<tr><td>' + row.location + '</td><td>' + stage + '</td><td>' + row.cheese + '</td><td>';

            if (show_per_hunt) {
                final_html += parseFloat(row.total_drops / row.total_hunts).toPrecision(3) + '</td><td>' + row.total_hunts + '</td>';
            } else {
                final_html += parseFloat(row.total_drops / row.total_catches).toPrecision(3) + '</td><td>' + row.total_catches + '</td>'
            }

            var drop_chance = parseFloat(row.drop_pct / 100);
            var moe = 1.96 * Math.sqrt(drop_chance * (1 - drop_chance) / row.total_catches) * 100;
            final_html += '<td>' + parseFloat(row.drop_pct).toFixed(2) + '%</td><td>' + moe.toFixed(2) + '%</td><td>' + row.min_amt + ' - ' + row.max_amt + '</td></tr>';
        })

        final_html += '</tbody></table>';
        $("#results").html(final_html);
        $('#results_table').DataTable({
            "paging": false,
            "searching": false,
            "info": false,
            "order": [[3, 'desc']],
            "columnDefs": [
                {
                    "targets": [1],
                    "visible": (all_stages.length === 0 ? false : true)
                }
            ]
        });

        var table = $('#results_table').DataTable();
        table.columns().iterator('column', function(ctx, idx) {
            $(table.column(idx).header()).append('<span class="sort-icon"/>');
        });

        $("#loader").css("display", "none");
    }
});
