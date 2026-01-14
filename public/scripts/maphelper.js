$(function() {
    $( "#reset" ).click(function() {
        $("#results").html('');
        $("textarea#mice").val('').focus();
    });
    $("textarea#mice").focus();
});
