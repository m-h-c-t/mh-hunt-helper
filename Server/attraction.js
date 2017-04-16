$( function() {
    $("#erase_mouse").click(function () {
        $("#mouse").val('').focus();
    });

    $('#mouse').autocomplete({
        source: "searchByMouse.php",
        minLength: 2,
        select: function( event, ui ) {
            $("#loader").show;
            getAttractionByMouse(ui.item.id);
        }
    });

    function getAttractionByMouse(mouse_id) {
        $.ajax({
            url: "searchByMouse.php",
            method: "POST",
            data: {
                mouse_id: mouse_id
            }
        })
        .done(function( data ) {
            renderResultsTable( JSON.parse(data));
        });
    };

    function renderResultsTable(data) {
        var final_html = '<table class="table"><tr><th>Location</th><th>Stage</th><th>Attracted</th><th>Total hunts</th><th>Rate</th></tr>';

        data.forEach(function(row) {
            final_html += '<tr><td>' + row.location +
              '</td><td>' + (row.stage ? row.stage : '') +
              '</td><td>' + row.attracted_hunts +
              '</td><td>' + row.total_hunts +
              '</td><td>' + ((row.attracted_hunts/row.total_hunts)*100).toFixed(2) + '%</td></tr>';
        });
        final_html += '</table>';
        $("#results").html(final_html);
        //$("#loader").hide;
    }
});
