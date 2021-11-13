$('#aggregated_locations').DataTable( {
    "paging":   false,
    "searching": false,
    "info": false,
    "order": [[1, 'desc']]
});
var table = $('#aggregated_locations').DataTable();
table.columns().iterator( 'column', function (ctx, idx) {
    $( table.column(idx).header() ).append('<span class="sort-icon"/>');
});
