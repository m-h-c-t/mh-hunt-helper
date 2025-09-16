$( function() {
    $("#erase_item").click(function () {
        $("#results").html('');
        $("#results_total").html('');
        $("#item").val('').focus();
        window.history.replaceState({}, "MH Hunt Helper", "converter.php");
    });

    searchItems('all', firstLoad);

    function searchItems(item_id, callback) {
        if (item_id !== 'all') {
            $("#loader").css( "display", "block" );
            // Every time we search for a item (on reload or ajax) set a history of it.
            window.history.replaceState({}, "MH Hunt Helper", "converter.php?item=" + item_id);
        }

        $.ajax({
            url: "searchByItem.php",
            method: "GET",
            data: {
                item_id: item_id,
                item_type: "convertible"
            },
            success: callback
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

                results.sort(function(a, b) {
                    return a.value.toUpperCase().indexOf(request.term.toUpperCase()) - b.value.toUpperCase().indexOf(request.term.toUpperCase());
                });

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
        var final_html = `<table id="results_table" class="table table-striped table-hover" style="width:100%">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Average Qty</th>
                    <th>Chance for any</th>
                    <th>Min-Max Qty / Slot</th>
                    <th>Gold Value</th>
                    <th>SB Value</th>
                </tr>
            </thead>
            <tbody>`;

        let total_seen = 'Did not find this convertible';
        let total_gold_value = 0;
        let total_sb_value = 0;
        data.forEach(row => {
            total_seen = row.total + ' convertibles. (' + row.single_opens + ' opened single)';

            const avg_qty_text = parseFloat((row.total_items / row.total).toPrecision(3));

            let chance_for_any_text;
            let min_max_qty_text;
            if (row.single_opens == 0 || row.min_item_quantity == null || row.max_item_quantity == null) {
                chance_for_any_text = 'N/A';
                min_max_qty_text = 'N/A';
            } else {
                chance_for_any_text = parseFloat((row.times_with_any / row.single_opens * 100).toPrecision(3)) + '&percnt;';

                if (row.min_item_quantity == row.max_item_quantity) {
                    min_max_qty_text = row.min_item_quantity;
                } else {
                    min_max_qty_text = row.min_item_quantity + '-' + row.max_item_quantity;
                }
            }

            let gold_value_text = '';
            let sb_value_text = '';
            if (row.item_gold_value) {
                gold_value_text = parseFloat((row.total_items / row.total * row.item_gold_value).toPrecision(3));

                total_gold_value += row.total_items / row.total * row.item_gold_value;
            }
            if (row.item_sb_value) {
                sb_value_text = parseFloat((row.total_items / row.total * row.item_sb_value).toPrecision(3));

                total_sb_value += row.total_items / row.total * row.item_sb_value;
            }

            final_html += `<tr>
                <td>${row.item}</td>
                <td>${avg_qty_text}</td>
                <td>${chance_for_any_text}</td>
                <td>${min_max_qty_text}</td>
                <td>${gold_value_text}</td>
                <td>${sb_value_text}</td>
            </tr>`;
        });
        final_html += '</tbody></table>';

        if (total_gold_value > 0) {
            total_seen += '<br>';
            total_seen += `Tradeable value per open: ${parseFloat(total_gold_value.toPrecision(3)).toLocaleString()}
                gold (${parseFloat(total_sb_value.toPrecision(3))} SB)`;
        }

        $('#results_total').html(`<h4>${total_seen}</h4>`);

        $("#results").html(final_html);
        $('#results_table').DataTable( {
            "paging":   false,
            "searching": false,
            "info": false,
            "columns": [
                { "type": "html" },
                { "type": "num-fmt" },
                { "type": "num-fmt" },
                { orderable: false },
                { "type": "num-fmt" },
                { "type": "num-fmt" },
            ],
            "order": [[1, 'desc']]
        });

        var table = $('#results_table').DataTable();
        table.columns().iterator( 'column', function (ctx, idx) {
            $( table.column(idx).header() ).append('<span class="sort-icon"/>');
        });

        $("#loader").css( "display", "none" );
    }
});
