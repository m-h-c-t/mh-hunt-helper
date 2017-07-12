(function() {

    if (!$('#fb-root').length) {
        listRequests();
        return;
    }

    // Facebook async load
    window.fbAsyncInit = function() {
        FB.init({
            appId      : '314857368939024',
            xfbml      : true,
            version    : 'v2.9'
        });
        FB.AppEvents.logPageView();

        // Check status on initial load
        FB.getLoginStatus(function(response) {
            statusChangeCallback(response);
        });
    };

    (function(d, s, id){
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {return;}
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));

    // This function is called when someone finishes with the Login Button
    window.checkLoginState = function() {
        FB.getLoginStatus(function(response) {
            statusChangeCallback(response);
        });
    }

    // Results from login status check
    function statusChangeCallback(response) {
        if (response.status === 'connected') {
            // Logged into your app and Facebook.
            $('.showOnLogin').show();
            $('.hideOnLogin').hide();
            $('#fbAccessToken').val(response.authResponse.accessToken);
            $('#fbUserId').val(response.authResponse.userID);
            FB.api('/' + response.authResponse.userID + '?fields=link,first_name', function(response2) {
                $('#fName').val(response2.first_name);
                $('#fbLink').val(response2.link);
            });
            listRequests(response.authResponse.userID);

        } else {
            // The person is not logged into your app or we are unable to tell.
            $('.showOnLogin').hide();
            $('.hideOnLogin').show();
        }
    }

    // logout function
    $('#logoutFB').click(function() {
        FB.logout(function(response) {
            $('.showOnLogin').hide();
            $('.hideOnLogin').show();
            $("#success_message").hide();
            $("#error_message").hide();
        });
    });

    // Autocomplete
    searchItems('all', addAutocomplete);

    function searchItems(item_id, callback) {

        $.ajax({
            url: "searchByItem.php",
            method: "POST",
            data: {
                item_id: item_id,
                item_type: "mhmh_mouse"
            }
        })
        .done(function( data ) {
            callback( JSON.parse(data));
        });
    }

    function addAutocomplete(items) {
        $('#mouseName').autocomplete({
            source: items,
            select: function( event, ui ) {
                $('#mouseId').val(ui.item.id);
            }
        });

        // Fix for double click on IOS
        if (navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
            $('#mouseName').autocomplete('widget').off('mouseenter');
        }
    }


    function listRequests(fbUserId) {
        var data = {mh_action: 'getAllRequests'};
        if (fbUserId !== undefined && fbUserId.length > 0) {
            data = {
                fbUserId: fbUserId,
                mh_action: 'getUserRequests',
                fbAccessToken: $('#fbAccessToken').val()
            };
        }
        $.ajax({
            url: "sniperRequests.php",
            data: data
        }).done(function(results) {
            results = JSON.parse(results);
            if (results.length === 0) {
                $('#currentRequests').html("<p>No requests found yet. Why don't you make one?</p>");
                return;
            }

            var final_html = '';
            results.forEach(function(row) {
                final_html += '<div class="table-responsive"><table class="table table-bordered" id="request_' + row.id + '">';

                // Admin section
                if (fbUserId !== undefined) {
                    if (row.timediff < 0 || row.man_expired == 1) {
                        // If expired
                        final_html +=
                            '<tr class="bg-danger"><td>Expired</td><td></td><td></td></tr>';
                    } else {
                        // If active
                        var hours   = Math.floor(row.timediff / 3600);
                        var minutes = Math.floor((row.timediff - (hours * 3600)) / 60);
                        final_html +=
                            '<tr><td class="bg-success">Active</td>'
                            + '<td>Timer: ' + hours + 'h ' + minutes + 'm </td>'
                            + '<td><button class="btn btn-danger expire_button" value="' + row.id + '">Expire</button></td></tr>';
                    }
                }

                // Actual request
                final_html +=
                    '<tr>'
                        + '<td><a target="_blank" href="https://mhmaphelper.agiletravels.com/mice/' + row.mouse + '"><button class="btn btn-warning">' + row.mouse + '</button></a></td>'
                            + '<td style="width:15%">' + row.reward_count + ' SB+</td>'
                        + '<td style="width:15%"><a target="_blank" href="' + row.fb_link + '"><button class="btn btn-primary">' + row.first_name + '</button></a></td>'
                    + '</tr></table></div>';
            });
            $('#currentRequests').html(final_html);
            $('.expire_button').click(expireRequest);
        });
    }

    // Create new request form
    $("#newPostForm").validate({
        debug: true,
        errorPlacement: function(error, element) {
            if (element.attr("name") == "mouseName" ) {
                error.insertAfter("#mouseInputGroup");
            } else if (element.attr("name") == "rewardCount" ) {
                error.insertAfter("#rewardInputGroup");
            } else {
                error.insertAfter(element);
            }
        },
        invalidHandler: function(event, validator) {
            // 'this' refers to the form
            var errors = validator.numberOfInvalids();
            $("#success_message").hide();
            $("#error_message").hide();
            if (errors) {
                var message = 'You missed ' + errors + ' field(s). They have been highlighted';
                $("#error_message").html(message);
                $("#error_message").show();
            }
        },
        submitHandler: function() {
            $("#success_message").hide();
            $("#error_message").hide();
            var data = $('#newPostForm').serializeArray();
            data.push({name:'mh_action', value:'createNewRequest'});

            $.ajax({
                method: "POST",
                url: "sniperRequests.php",
                data: data
            }).done(function(response) {
                $('#mouseName').val('');
                $('#rewardCount').val('');
                $('#mouseId').val('');
                if (response === 'Request added!') {
                    $("#success_message").html(response);
                    listRequests($('#fbUserId').val());
                    $("#success_message").show();
                } else {
                    $("#error_message").html(response);
                    $("#error_message").show();
                }
            });
        }
    });

    function expireRequest() {
        var data = $('#newPostForm').serializeArray();
        data.push({name:'request_id', value:$(this).val()});
        data.push({name:'mh_action', value:'expireRequest'});
        $("#success_message").hide();
        $("#error_message").hide();
        $.ajax({
            method: "POST",
            url: "sniperRequests.php",
            data: data,
        }).done(function(response) {
            if (response === 'Request expired!') {
                $("#success_message").html(response);
                listRequests($('#fbUserId').val());
                $("#success_message").show();
            } else {
                $("#error_message").html(response);
                $("#error_message").show();
            }
        });
    };

})();