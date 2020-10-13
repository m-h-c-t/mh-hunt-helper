$(function() {
    $( "#reset" ).click(function() {
        $("#results").html('');
        $("textarea#items").val('').focus();
    });
    $("textarea#items").focus();
});
