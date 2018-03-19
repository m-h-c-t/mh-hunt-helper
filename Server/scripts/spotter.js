(function() {

    $("#loader").css( "display", "block" );

    if (!$('#fb-root').length) {
        $('#typeFilters > button').on('click', function() {
            listRequests(undefined, $(this).val());
            $('#typeFilters > button').toggleClass("active", false);
            $(this).toggleClass("active", true);
        });

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
            getFBUserInfo();
            listRequests(response.authResponse.userID);

        } else {
            // The person is not logged into your app or we are unable to tell.
            $('.showOnLogin').hide();
            $('.hideOnLogin').show();
            $("#loader").css( "display", "none" );
        }
    }

    function getFBUserInfo() {
        FB.api('/' + $('#fbUserId').val() + '?fields=link,first_name,permissions', function(response) {
            $('#fName').val(response.first_name);
        });
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

    // Mice and Maps Autocomplete
    searchItems('mice', addAutocomplete);
    searchItems('maps', addAutocomplete);

    function searchItems(item_type, callback) {
        if (item_type === 'mice') {
            var data = {
                item_type: "mhmh_mouse"
            };
        } else if (item_type === 'maps') {
            var data = {
                item_type: "map"
            }
        }
        data.item_id = 'all';

        $.ajax({
            url: "searchByItem.php",
            method: "GET",
            data: data
        })
        .done(function( data ) {
            callback(item_type, JSON.parse(data));
        });
    }

    function addAutocomplete(item_type, items) {
        if (item_type === 'mice') {
            var name_field = 'mouseName';
            var id_field = 'mouseId';
        } else if (item_type === 'maps') {
            var name_field = 'mapName';
            var id_field = 'mapId';
        }

        $('#' + name_field).autocomplete({
            source: function(request, response) {
                var results = $.ui.autocomplete.filter(items, request.term);
                response(results.slice(0, 10));
            },
            delay: 0,
            select: function( event, ui ) {
                $('#' + id_field).val(ui.item.id);
            }
        });

        // Fix for double click on IOS
        if (navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
            $('#' + name_field).autocomplete('widget').off('mouseenter');
        }
    }

    function listRequests(fbUserId, typeFilter) {
        $("#loader").css( "display", "block" );
        var request_types = {
            snipe_request: "Snipe Request",
            snipe_offer: "Snipe Offer",
            leech_request: "Leech Request",
            leech_offer: "Leech Offer",
            helper_request: "Helper Request",
            helper_offer: "Helper Offer"
        };
        var data = {mh_action: 'getAllRequests'};
        if (fbUserId !== undefined && fbUserId.length > 0) {
            data = {
                fbUserId: fbUserId,
                mh_action: 'getUserRequests',
                fbAccessToken: $('#fbAccessToken').val()
            };
        }
        if (typeFilter !== undefined && typeFilter.length > 0) {
            data.typeFilter = typeFilter;
        }
        $.ajax({
            url: "sniperRequests.php",
            data: data
        }).done(function(results) {
            results = JSON.parse(results);
            if (results.length === 0) {
                $('#currentRequests').html("<p>No requests found yet. Why don't you make one?</p>");
                $("#loader").css( "display", "none" );
                return;
            }

            var final_html = '';
            var highlight = '';
            var rounded_top = '';
            var rounded_left_top = '';
            var rounded_right_top = '';
            results.forEach(function(row) {
                rounded_top = 'border-top-rounded';
                rounded_left_top = 'border-top-left-rounded';
                rounded_right_top = 'border-top-right-rounded';
                if (window.location.hash && window.location.hash === ('#' + row.id)) {
                    highlight = 'highlight';
                } else {
                    highlight = '';
                }
                final_html += '<div class="table-responsive"><table class="table table-bordered border-top-rounded border-bottom-rounded ' + highlight + '" style="border-collapse:separate;" id="' + row.id + '">';

                // Admin section
                if (fbUserId !== undefined) {
                    var enddate = parseInt(row.timestamp) + 48*60*60;
                    var now = Math.floor(new Date().getTime()/1000);
                    var timediff = enddate - now;
                    if (timediff < 0 || row.man_expired == 1) {
                        // If expired
                        final_html +=
                            '<tr class="bg-danger ' + rounded_top + '"><td class="' + rounded_top + '" colspan="4"><strong>Expired</strong></td></tr>';
                    } else {
                        // If active
                        var hours   = Math.floor(timediff / 3600);
                        var minutes = Math.floor((timediff - (hours * 3600)) / 60);
                        final_html +=
                            '<tr class="' + rounded_top + '"><td class="bg-success ' + rounded_left_top + '"><strong>Active</strong></td>'
                            + '<td id="timer_' + row.id + '" colspan="2">' + hours + 'h ' + minutes + 'm </td>'
                            + '<td class="bg-danger ' + rounded_right_top + '"><button class="btn btn-danger btn-block expire_button" value="' + row.id + '">Expire</button></td></tr>';
                    }
                    rounded_top = '';
                    rounded_left_top = '';
                    rounded_right_top = '';
                }

                // Actual request
                final_html +=
                    '<tr class="border-bottom-rounded ' + rounded_top + '">'
                        + '<td class="border-bottom-left-rounded ' + rounded_left_top + '" style="width:20%;background-color:#eee;"><button class="btn btn-default btn-block"><strong>' + request_types[row.request_type] + '</strong></button></td>';
                if (row.mouse) {
                    final_html += '<td class="bg-warning"><a target="_blank" href="https://mhmaphelper.agiletravels.com/mice/' + row.mouse + '"><button class="btn btn-warning">' + row.mouse + '</button></a></td>';
                } else {
                    final_html += '<td class="bg-warning"><a target="_blank" href="https://mhhunthelper.agiletravels.com/mapper.php?item=' + row.map_id + '"><button class="btn btn-warning">' + (row.dusted == 1 ? 'Dusted ' : '' ) + row.map + (row.dusted == 2 ? ' (Split Dust)' : '' ) + '</button></a></td>';
                }

                final_html += '<td style="width:15%" class="bg-success">';
                if (row.reward_count) {
                    final_html += '<button class="btn btn-success btn-block">' + row.reward_count + ' SB+</button>';
                }
                final_html += '</td>'
                        + '<td style="width:15%;" class="bg-info border-bottom-right-rounded ' + rounded_right_top + '"><a target="_blank" href="https://www.facebook.com/app_scoped_user_id/' + row.fb_id + '/"><button class="btn btn-primary btn-block">' + row.first_name + '</button></a></td>'
                    + '</tr></table></div>';
            });
            $('#currentRequests').html(final_html);
            $('.expire_button').click(expireRequest);
            if (window.location.hash && $(window.location.hash).length) {
                $('html,body').animate({scrollTop: $(window.location.hash).offset().top});
            }
            $("#loader").css( "display", "none" );
        });
    }

    // Post type management
    viewPostFields();
    $('#postType').change(viewPostFields);

    function viewPostFields() {
        switch($('#postType').val()) {
            case 'snipe_request':
            case 'snipe_offer':
                $('#mouseInputGroup').show();
                $('#mapInputGroup').hide();
                $('#rewardInputGroup').show();
                break;
            case 'leech_request':
            case 'leech_offer':
                $('#mouseInputGroup').hide();
                $('#mapInputGroup').show();
                $('#rewardInputGroup').show();
                break;
            case 'helper_request':
            case 'helper_offer':
                $('#mouseInputGroup').hide();
                $('#mapInputGroup').show();
                $('#rewardInputGroup').hide();
                break;
        }
    }

    // Create new request form
    $("#newPostForm").validate({
        debug: true,
        errorPlacement: function(error, element) {
            error.insertAfter(element.parent('div'));
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
            $("#loader").css( "display", "block" );
            $("#success_message").hide();
            $("#error_message").hide();
            var data = $('#newPostForm').serializeArray();
            data.push({name:'mh_action', value:'createNewRequest'});
            $.ajax({
                method: "POST",
                url: "sniperRequests.php",
                data: data
            }).done(function(response) {
                if (response === 'Request added!') {
                    $('#mouseName').val('');
                    $('#rewardCount').val('');
                    $('#mouseId').val('');
                    $('#mapName').val('');
                    $('#mapId').val('');
                    $("#success_message").html(response);
                    listRequests($('#fbUserId').val());
                    $("#success_message").show();
                } else {
                    $("#error_message").html(response);
                    $("#error_message").show();
                    $("#loader").css( "display", "none" );
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